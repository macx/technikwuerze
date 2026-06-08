/** FONTS */
import '@fontsource-variable/bricolage-grotesque/standard.css'
import '@fontsource-variable/manrope'
import '@fontsource-variable/caveat'
import '@fontsource-variable/material-symbols-outlined/fill.css'

/* STYLES */
import './styles/main.css'

/* SCRIPTS */
import { initHeaderNav } from './scripts/components/header-nav'
import { initSearchDialog } from './scripts/components/search-dialog'
import { initModeSwitch } from './scripts/components/theme-switch'
import { initViewTransitions } from './scripts/components/view-transitions'

/* Podlove-Player */
import { initPodlovePlayers } from '/site/plugins/kirby-tw-transcript/src/podlove-player.ts'
import '/site/plugins/kirby-tw-transcript/assets/tw-transcript.css'

import { initKomments } from './scripts/components/komments'

document.addEventListener('DOMContentLoaded', () => {
  initHeaderNav()
  initModeSwitch()
  initSearchDialog()
  initPodlovePlayers()
  initViewTransitions()
  initKomments()

  if (document.querySelector('.tw-brand-networks')) {
    void import('./scripts/components/brand-networks').then(({ initBrandNetworks }) => {
      initBrandNetworks()
    })
  }
})
