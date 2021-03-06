import Vue from 'vue'
import Router from 'vue-router'
import FormVue from '@/components/formVue'
import Login from '@/components/Login'
import NotFound from '@/components/NotFound'

import userList from '@/components/userList'
import addUser from '@/components/addUser'
import editUser from '@/components/editUser'
// import resetPwd from '@/components/resetPwd'

import carList from '@/components/carList'
import addCar from '@/components/addCar'
import editCar from '@/components/editCar'

import financeList from '@/components/financeList'

Vue.use(Router)
const modules = [{
  path: '/user',
  name: '用户管理',
  component: userList,
  child: [{
    path: '/user',
    name: '用户列表',
    component: userList
  }, {
    path: '/add-user',
    name: '添加用户',
    component: addUser
  // }, {
    // path: '/change-pwd',
    // name: '修改密码',
    // component: resetPwd
  }]
}, {
  path: '/car',
  name: '车辆管理',
  component: carList,
  child: [{
    path: '/car',
    name: '车辆列表',
    component: carList
  }, {
    path: '/add-car',
    name: '添加车辆',
    component: addCar
  }, {
    path: '/issue-car',
    name: '发布车辆',
    component: FormVue
  }]
}, {
  path: '/finance',
  name: '财务管理',
  component: financeList,
  child: [{
    path: '/total-asset',
    name: '总利润',
    component: userList
  }, {
    path: '/self-funds',
    name: '自有资金',
    component: userList
  }, {
    path: '/prepare-cost',
    name: '整备费用',
    component: userList
  }, {
    path: '/investment-amount',
    name: '投资金额',
    component: userList
  }, {
    path: '/distribute-profit',
    name: '分配利润',
    component: userList
  }]
}]
let routes = [{
  path: '/',
  redirect: '/car'
}, {
  name: '编辑用户',
  path: '/edit-user',
  component: editUser
}, {
  name: '编辑车辆',
  path: '/edit-car',
  component: editCar
}, {
  name: '登出系统',
  path: '/login',
  component: Login
}, {
  path: '*',
  name: 'error',
  component: NotFound
}]
for (let key in modules) {
  let route = modules[key]
  if (route.path !== undefined) {
    routes = routes.concat(route)
  }
  if (route.child !== undefined) {
    for (let k in route.child) {
      let tmp = route['child'][k]
      if (undefined === tmp.meta) {
        tmp.meta = {}
      }
      tmp.meta.parent = route.name
      tmp.meta.key = key
      routes = routes.concat(tmp)
    }
  }
}
for (let i = 0, len = routes.length; i < len; i++) {
  if (undefined === routes[i]['meta']) {
    routes[i]['meta'] = {}
  }
}
const router = new Router({
  modules: modules,
  mode: 'history',
  routes: routes
})
export default router
