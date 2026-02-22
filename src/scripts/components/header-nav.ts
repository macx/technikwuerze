export const initHeaderNav = (): void => {
  const nav = document.querySelector<HTMLElement>('#main-nav')
  if (!nav) {
    return
  }

  const navList = nav.querySelector<HTMLUListElement>('#main-nav-list')
  const navButtonTpl = nav.querySelector<HTMLTemplateElement>('#main-nav-button')
  const headerTools = document.querySelector<HTMLElement>('.header-tools')

  if (!navList || !navButtonTpl || !headerTools) {
    return
  }

  const navButtonClone = navButtonTpl.content.cloneNode(true) as DocumentFragment
  const navButton = navButtonClone.querySelector<HTMLButtonElement>('button')
  const navIcon = navButtonClone.querySelector<HTMLElement>('.msi-menu, .msi-close')

  if (!navButton) {
    return
  }

  // Keep existing CSS hooks for mobile open/close and desktop hiding.
  navButton.classList.add('main-nav__toggle')

  const setExpanded = (expanded: boolean): void => {
    navButton.setAttribute('aria-expanded', String(expanded))
    navButton.setAttribute('aria-label', expanded ? 'Menü schließen' : 'Menü')

    if (navIcon) {
      navIcon.classList.toggle('msi-menu', !expanded)
      navIcon.classList.toggle('msi-close', expanded)
    }
  }

  navButton.addEventListener('click', () => {
    const expanded = navButton.getAttribute('aria-expanded') === 'true'
    setExpanded(!expanded)
  })

  nav.addEventListener('keyup', (event) => {
    if (event.code === 'Escape') {
      setExpanded(false)
      navButton.focus()
    }
  })

  const navListItems = navList.querySelectorAll<HTMLAnchorElement>('a')
  if (navListItems.length > 0) {
    const currentPath = `${globalThis.location.pathname}${globalThis.location.search}`

    for (const item of navListItems) {
      const itemUrl = new URL(item.href, globalThis.location.href)
      const itemPath = `${itemUrl.pathname}${itemUrl.search}`

      if (itemPath === currentPath) {
        item.setAttribute('aria-current', 'page')
      }

      item.addEventListener('click', () => {
        navList.querySelector('[aria-current="page"]')?.removeAttribute('aria-current')
        item.setAttribute('aria-current', 'page')
        setExpanded(false)
      })
    }
  }

  headerTools.append(navButtonClone)
}
