// Supports weights 200-800
import '@fontsource-variable/bricolage-grotesque/standard.css'
// Supports weights 200-800
import '@fontsource-variable/manrope'
// Supports weights 400-700
import '@fontsource-variable/caveat'
// Supports weights 100-700
import '@fontsource-variable/material-symbols-outlined/fill.css'

import './styles/main.css'
import { initHeaderNav } from './scripts/components/header-nav'
import { initPodlovePlayers } from './scripts/components/podlove-player'
import { initModeSwitch } from './scripts/components/theme-switch'

console.log('Technikwürze - Vite is running!')

document.addEventListener('DOMContentLoaded', () => {
  initHeaderNav()
  initModeSwitch()
  initPodlovePlayers()
})
