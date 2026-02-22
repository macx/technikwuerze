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

const applyMode = (mode: ModeSetting): void => {
  root.setAttribute('mode', mode)
}

const syncModeSelects = (mode: ModeSetting): void => {
  for (const select of document.querySelectorAll<HTMLSelectElement>('[data-mode-select]')) {
    select.value = mode
  }
}

const saveMode = (mode: ModeSetting): void => {
  localStorage.setItem(MODE_STORAGE_KEY, mode)
}

export const initModeSwitch = (): void => {
  const initialMode = getStoredMode() ?? 'system'
  applyMode(initialMode)
  syncModeSelects(initialMode)

  for (const select of document.querySelectorAll<HTMLSelectElement>('[data-mode-select]')) {
    select.addEventListener('change', () => {
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

    switchRoot.dataset.enhanced = 'true'
  }

  darkMediaQuery.addEventListener('change', () => {
    const current = (root.getAttribute('mode') ?? 'system') as ModeSetting
    if (current === 'system') {
      applyMode('system')
    }
  })
}
