/** FONTS */
import '@fontsource-variable/bricolage-grotesque/standard.css'
import '@fontsource-variable/manrope'
import '@fontsource-variable/caveat'
import '@fontsource-variable/material-symbols-outlined/fill.css'

/* STYLES */
import './styles/main.css'

/* SCRIPTS */
import { initHeaderNav } from './scripts/components/header-nav'
import { initPodlovePlayers } from './scripts/components/podlove-player'
import { initModeSwitch } from './scripts/components/theme-switch'

document.addEventListener('DOMContentLoaded', () => {
  initHeaderNav()
  initModeSwitch()
  initPodlovePlayers()

  if (document.querySelector('.tw-brand-networks')) {
    void import('./scripts/components/brand-networks').then(({ initBrandNetworks }) => {
      initBrandNetworks()
    })
  }
})
