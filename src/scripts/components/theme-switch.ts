type ModeSetting = 'system' | 'dark' | 'light'

const MODE_STORAGE_KEY = 'twz-mode'
const root = document.documentElement
const darkMediaQuery = globalThis.matchMedia('(prefers-color-scheme: dark)')

const isModeSetting = (value: string | null): value is ModeSetting =>
  value === 'system' || value === 'dark' || value === 'light'

const getStoredMode = (): ModeSetting | null => {
  const stored = localStorage.getItem(MODE_STORAGE_KEY)
  return isModeSetting(stored) ? stored : null
}

const getEffectiveMode = (mode: ModeSetting): 'light' | 'dark' => {
  if (mode === 'light' || mode === 'dark') {
    return mode
  }
  return darkMediaQuery.matches ? 'dark' : 'light'
}

const applyMode = (mode: ModeSetting): void => {
  root.setAttribute('mode', mode)
  root.dispatchEvent(
    new CustomEvent('twz:modechange', {
      detail: {
        mode,
        effectiveMode: getEffectiveMode(mode),
      },
    })
  )
}

const syncModeSelects = (mode: ModeSetting): void => {
  for (const select of document.querySelectorAll<HTMLSelectElement>('[data-mode-select]')) {
    select.value = mode
  }
}

const saveMode = (mode: ModeSetting): void => {
  localStorage.setItem(MODE_STORAGE_KEY, mode)
}

const closeMainNav = (): void =>
  document.querySelector<HTMLButtonElement>('.main-nav__toggle[aria-expanded="true"]')?.click()

export const initModeSwitch = (): void => {
  const initialMode = getStoredMode() ?? 'system'
  applyMode(initialMode)
  syncModeSelects(initialMode)

  for (const select of document.querySelectorAll<HTMLSelectElement>('[data-mode-select]')) {
    select.addEventListener('change', () => {
      closeMainNav()
      const nextMode: ModeSetting = isModeSetting(select.value) ? select.value : 'system'
      applyMode(nextMode)
      syncModeSelects(nextMode)
      saveMode(nextMode)
    })
  }

  for (const switchRoot of document.querySelectorAll<HTMLElement>('[data-theme-switch]')) {
    const select = switchRoot.querySelector<HTMLSelectElement>('[data-mode-select]')

    if (!select) {
      continue
    }

    switchRoot.addEventListener('click', () => {
      closeMainNav()
    })

    switchRoot.dataset.enhanced = 'true'
  }

  darkMediaQuery.addEventListener('change', () => {
    const current = (root.getAttribute('mode') ?? 'system') as ModeSetting
    if (current === 'system') {
      applyMode('system')
    }
  })
}
