import Vuex from 'vuex'
import Vue from 'vue'

Vue.use(Vuex)

const mutations = {
  setAuth: (state, val) => {
    state.auth = val
  }
}
const actions = {}
const state = {
  auth: false
}

export default new Vuex.Store({
  state,
  actions,
  mutations
})
