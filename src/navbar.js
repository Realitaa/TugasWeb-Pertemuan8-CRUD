import { getStorage, setStorage, scrollToElement } from './utils.js'

export const navLinks = [
  { label: 'Produk', href: '#product' },
  { label: 'Tentang', href: '#about' },
  { label: 'Kontak', href: '#contact' },
]

export function setupNavbar() {
  const toggleBtn = document.getElementById('hs-navbar-example-collapse')
  const navCollapse = document.getElementById('hs-navbar-example')
  const navList = document.getElementById('navbar-list')

  // Helper untuk mengubah style link aktif / inaktif
  function setActiveLink(activeHref) {
    if (!navList) return
    navList.querySelectorAll('a[href^="#"]').forEach(a => {
      const isCurrent = a.getAttribute('href') === activeHref
      if (isCurrent) {
        a.className = 'focus:outline-hidden cursor-pointer'
        a.setAttribute('aria-current', 'page')
      } else {
        a.className = 'text-sm text-muted hover:text-brand-hover focus:outline-hidden cursor-pointer'
        a.removeAttribute('aria-current')
      }
    })
  }

  if (navList) {
    navList.innerHTML = navLinks.map((link, idx) => {
      const activeClass = idx === 0 
        ? 'focus:outline-hidden cursor-pointer'
        : 'text-sm text-muted hover:text-brand-hover focus:outline-hidden cursor-pointer'
      const ariaCurrent = idx === 0 ? 'aria-current="page"' : ''
      return `<li><a class="${activeClass}" href="${link.href}" ${ariaCurrent}>${link.label}</a></li>`
    }).join('')

    navList.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', (e) => {
        const targetId = anchor.getAttribute('href')
        if (targetId && targetId.startsWith('#')) {
          e.preventDefault()
          setActiveLink(targetId)
          scrollToElement(targetId)

          if (navCollapse && !navCollapse.classList.contains('hidden')) {
            navCollapse.classList.add('hidden')
            toggleBtn?.setAttribute('aria-expanded', 'false')
          }
        }
      })
    })

    // IntersectionObserver untuk mendeteksi perubahan section yang sedang dilihat user
    const sectionIds = navLinks.map(l => l.href.replace('#', ''))
    const sections = sectionIds.map(id => document.getElementById(id)).filter(Boolean)

    if ('IntersectionObserver' in window && sections.length > 0) {
      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            setActiveLink(`#${entry.target.id}`)
          }
        })
      }, {
        root: null,
        rootMargin: '-20% 0px -60% 0px',
        threshold: 0
      })

      sections.forEach(sec => observer.observe(sec))
    }
  }

  if (toggleBtn && navCollapse) {
    toggleBtn.addEventListener('click', () => {
      const isHidden = navCollapse.classList.contains('hidden')
      if (isHidden) {
        navCollapse.classList.remove('hidden')
        toggleBtn.setAttribute('aria-expanded', 'true')
      } else {
        navCollapse.classList.add('hidden')
        toggleBtn.setAttribute('aria-expanded', 'false')
      }
    })
  }

  // Theme switcher: light -> dark -> system -> light
  const themeToggleBtn = document.getElementById('theme-toggle-btn')
  const themeToggleIcon = document.getElementById('theme-toggle-icon')
  const themes = ['light', 'dark', 'system']
  
  const savedTheme = getStorage('theme', 'system')
  let currentThemeIndex = themes.includes(savedTheme) ? themes.indexOf(savedTheme) : themes.indexOf('system')

  function applyTheme(theme) {
    const isDark = theme === 'dark' || (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)
    if (isDark) {
      document.documentElement.classList.add('dark')
    } else {
      document.documentElement.classList.remove('dark')
    }

    if (themeToggleIcon) {
      if (theme === 'light') {
        themeToggleIcon.innerHTML = '<i data-lucide="sun" class="size-4"></i>'
      } else if (theme === 'dark') {
        themeToggleIcon.innerHTML = '<i data-lucide="moon" class="size-4"></i>'
      } else {
        themeToggleIcon.innerHTML = '<i data-lucide="laptop" class="size-4"></i>'
      }
    }
  }

  applyTheme(themes[currentThemeIndex])

  window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
    if (themes[currentThemeIndex] === 'system') {
      applyTheme('system')
    }
  })

  if (themeToggleBtn) {
    themeToggleBtn.addEventListener('click', () => {
      currentThemeIndex = (currentThemeIndex + 1) % themes.length
      const nextTheme = themes[currentThemeIndex]
      setStorage('theme', nextTheme)
      applyTheme(nextTheme)
    })
  }
}
