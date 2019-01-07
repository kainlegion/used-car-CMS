<style>
html,
body,
#app {
  background: #f2f2f2;
}

.container {
  margin: 0 10%;
  height: 100%;
}

.mt-large {
  margin-top: 24px;
}
</style>
<template>
<div id="app">
  <Header v-if="$store.state.auth"></Header>
  <Breadcrumb class="container mt-large" v-if="$store.state.auth" separator=">">
    <BreadcrumbItem>Home</BreadcrumbItem>
    <BreadcrumbItem v-if="$router.currentRoute.meta.parent !== undefined">{{$router.currentRoute.meta.parent}}</BreadcrumbItem>
    <BreadcrumbItem>{{$router.currentRoute.name}}</BreadcrumbItem>
  </Breadcrumb>
  <Card v-if="$store.state.auth && $router.currentRoute.name !== 'error'" class="container mt-large" :padding="32">
    <router-view :sys-name="sysName" />
  </Card>
  <router-view v-else :sys-name="sysName" class="container mt-large"></router-view>
</div>
</template>

<script>
import Header from '@/components/Header'

export default {
  name: '#app',
  data () {
    return {
      sysName: 'used-car-CMS'
    }
  },
  components: {
    Header
  }
}
</script>
