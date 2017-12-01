// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App'
import router from './router'
import VueResource from 'vue-resource'
import store from './store/index'
import iView from 'iview/dist/iview.min'
import 'iview/dist/styles/iview.css'

Vue.config.productionTip = false

Vue.use(VueResource)
Vue.use(iView)

Vue.http.options.emulateJSON = true

router.beforeEach((to, from, next) => {
  if (to.path === '/login') {
    Vue.http.get('api/index.php?c=auth&a=signout')
    store.commit('setAuth', false)
  } else {
    Vue.http.get('api/index.php?c=auth&a=check').then((responses) => {
      store.commit('setAuth', (responses.data.state === '200'))
      if (!store.state.auth) {
        next({path: '/login'})
      }
    })
  }
  next()
})

console.log(router)

/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  store,
  template: '<App/>',
  components: {App}
})
