import { defineAsyncComponent } from 'vue'
import AlertList from '@/components/AlertList'

export default function loadGlobalComponents(app) {
  app.component('AlertList', AlertList)
  app.component('apexchart', defineAsyncComponent(() => import('./plugins/charts')))
}
