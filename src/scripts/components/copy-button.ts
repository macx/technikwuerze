export const initCopyButtons = (): void => {
  document.addEventListener('click', async (event) => {
    const button = (event.target as HTMLElement).closest<HTMLButtonElement>('[data-copy-text]')
    if (!button) {
      return
    }

    const label = button.querySelector('span') ?? button
    const originalLabel = label.textContent ?? ''

    try {
      await navigator.clipboard.writeText(button.dataset.copyText ?? '')
      label.textContent = button.dataset.copiedLabel ?? 'Kopiert'
      window.setTimeout(() => {
        label.textContent = originalLabel
      }, 2500)
    } catch {
      label.textContent = originalLabel
    }
  })
}
