import VueApexCharts from 'vue-apexcharts'
import AlertList from '@/components/AlertList'

export default function loadGlobalComponents(Vue) {
  Vue.component('AlertList', AlertList)

  Vue.use(VueApexCharts)
  Vue.component('apexchart', VueApexCharts)
}
