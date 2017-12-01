import Vue from 'vue'
import Router from 'vue-router'
import TableVue from '@/components/tableVue'
import FormVue from '@/components/formVue'
import Login from '@/components/Login'
import NotFound from '@/components/NotFound'

Vue.use(Router)

const modules = [
  {
    path: '/user',
    name: '会员管理',
    component: TableVue,
    child: [
      {
        path: '/add-user',
        name: '添加客户',
        component: FormVue
      },
      {
        path: '/change-pwd',
        name: '修改密码',
        component: FormVue
      }
    ]
  },
  {
    path: '/car',
    name: '车辆管理',
    component: TableVue,
    child: [
      {
        path: '/add-car',
        name: '添加车辆',
        component: FormVue
      },
      {
        path: '/issue-car',
        name: '发布车辆',
        component: FormVue
      }
    ]
  },
  {
    path: '/asset',
    name: '财务管理',
    component: TableVue,
    child: [
      {
        path: '/total-asset',
        name: '总利润',
        component: TableVue
      },
      {
        path: '/self-funds',
        name: '自有资金',
        component: TableVue
      },
      {
        path: '/prepare-cost',
        name: '整备费用',
        component: TableVue
      },
      {
        path: '/investment-amount',
        name: '投资金额',
        component: TableVue
      },
      {
        path: '/distribute-profit',
        name: '分配利润',
        component: TableVue
      }
    ]
  }
]

let routes = [
  {
    path: '/',
    redirect: '/user'
  }, {
    name: '登出系统',
    path: '/login',
    component: Login
  }, {
    path: '*',
    name: 'error',
    component: NotFound
  }
]

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
  mode: 'history',
  modules: modules,
  routes: routes
})

export default router
