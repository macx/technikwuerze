export const initViewTransitions = (): void => {
  if (!CSS.supports('view-transition-name: participant-name')) {
    return
  }

  const normalizeGroup = (value: string | undefined): string | null => {
    const group = value?.trim()
    return group ? group : null
  }

  const resolveTransitionClass = (element: HTMLElement): string | null => {
    const explicitGroup = normalizeGroup(element.dataset.vtGroup)
    if (explicitGroup) {
      return explicitGroup
    }

    if (element.classList.contains('participant-name')) {
      return 'participant-name'
    }

    if (element.classList.contains('participant-image')) {
      return 'participant-image'
    }

    return null
  }

  for (const element of document.querySelectorAll<HTMLElement>('[data-vt-name]')) {
    const transitionName = element.dataset.vtName?.trim()
    if (!transitionName) {
      continue
    }

    element.style.viewTransitionName = transitionName

    const transitionClass = resolveTransitionClass(element)
    if (transitionClass) {
      element.style.viewTransitionClass = transitionClass
    }
  }
}
