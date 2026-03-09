const closeOpenMainNav = (): void =>
  document.querySelector<HTMLButtonElement>('.main-nav__toggle[aria-expanded="true"]')?.click()

export const initSearchDialog = (): void => {
  const dialog = document.querySelector<HTMLDialogElement>('[data-search-dialog]')

  if (!dialog || typeof dialog.showModal !== 'function') {
    return
  }

  const input = dialog.querySelector<HTMLInputElement>('[data-search-dialog-input]')
  const category = dialog.querySelector<HTMLSelectElement>('#site-search-dialog-category')

  for (const button of document.querySelectorAll<HTMLButtonElement>('[data-search-dialog-open]')) {
    button.addEventListener('click', () => {
      closeOpenMainNav()
      if (category) {
        category.value = 'content'
      }
      dialog.showModal()
      input?.focus()
      input?.select()
    })
  }

  for (const button of dialog.querySelectorAll<HTMLButtonElement>('[data-search-dialog-close]')) {
    button.addEventListener('click', () => {
      dialog.close()
    })
  }

  dialog.addEventListener('click', (event) => {
    if (event.target === dialog) {
      dialog.close()
    }
  })
}
