export function initKomments() {
  // Handle dynamically loaded elements (e.g. after View Transitions) using event delegation
  document.addEventListener('click', (event) => {
    const target = event.target as HTMLElement

    // 1. Handle Click on Reply Link
    const replyLink = target.closest<HTMLAnchorElement>('.kommentReply')
    if (replyLink) {
      const handle = replyLink.getAttribute('data-handle')
      const targetId = replyLink.getAttribute('data-id')

      const formTitle = document.getElementById('kommentform-title')
      const cancelBtn = document.getElementById('kommentform-cancel-reply')
      const jumpBtn = document.getElementById('kommentform-jump-reply') as HTMLAnchorElement | null
      const replyToInput = document.querySelector<HTMLInputElement>('input[name="replyTo"]')
      const replyHandleInput = document.querySelector<HTMLInputElement>('input[name="replyHandle"]')
      const commentTextarea = document.getElementById('comment') as HTMLTextAreaElement | null

      if (formTitle && cancelBtn && jumpBtn && handle && targetId) {
        formTitle.textContent = `Antwort an ${handle}`
        cancelBtn.hidden = false
        jumpBtn.hidden = false
        jumpBtn.href = `#c${targetId}`
      }

      if (targetId && replyToInput) {
        replyToInput.value = targetId
      }

      if (handle && replyHandleInput) {
        replyHandleInput.value = handle
      }

      // Barrierefreiheit: Fokus in das Eingabefeld setzen
      if (commentTextarea) {
        commentTextarea.focus()
      }

      return
    }

    // 2. Handle Click on Cancel Reply (X Button)
    const cancelBtn = target.closest<HTMLButtonElement>('#kommentform-cancel-reply')
    if (cancelBtn) {
      const formTitle = document.getElementById('kommentform-title')
      const jumpBtn = document.getElementById('kommentform-jump-reply') as HTMLAnchorElement | null
      const replyToInput = document.querySelector<HTMLInputElement>('input[name="replyTo"]')
      const replyHandleInput = document.querySelector<HTMLInputElement>('input[name="replyHandle"]')

      // Reset inputs
      if (replyToInput) replyToInput.value = ''
      if (replyHandleInput) replyHandleInput.value = ''

      // Reset title
      if (formTitle) {
        const defaultTitle = formTitle.getAttribute('data-default-title')
        if (defaultTitle) formTitle.textContent = defaultTitle
      }

      // Hide buttons
      cancelBtn.hidden = true
      if (jumpBtn) {
        jumpBtn.hidden = true
        jumpBtn.href = '#'
      }
    }
  })

  // 3. Handle Form Submission (AJAX)
  document.addEventListener('submit', (event) => {
    const target = event.target as HTMLFormElement
    if (target.id === 'kommentform') {
      event.preventDefault()

      const formAction = target.action
      const formLoader = target.querySelector('.loader')
      const formMsg = target.querySelector('.user-feedback')

      if (formLoader) formLoader.classList.add('visible')
      if (formMsg) {
        formMsg.classList.remove('msg-error', 'msg-success', 'visible')
      }

      fetch(formAction, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-Return-Type': 'json' },
        body: JSON.stringify(Object.fromEntries(new FormData(target))),
      })
        .then(async (response) => {
          if (formLoader) formLoader.classList.remove('visible')

          if (!response.ok) {
            throw response
          }
          return response.json()
        })
        .then((response) => {
          if (formMsg) {
            formMsg.innerHTML = `<p>${response.message}</p>`
            formMsg.classList.add('msg-success', 'visible')
          }
          target.reset()
        })
        .catch(async (response) => {
          let message = 'An error occurred'
          try {
            const error = await response.json()
            message = error.message || message
          } catch (e) {
            // failed to parse json
          }

          if (formMsg) {
            formMsg.innerHTML = `<p>${message}</p>`
            formMsg.classList.add('msg-error', 'visible')
          }
        })
    }
  })
}
