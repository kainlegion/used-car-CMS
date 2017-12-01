<style>
    .logo {
        float: left;
        height: 60px;
        max-height: 100%;
        max-width: 100%;
    }

    .nav-list {
        float: left;
        margin-left: 48px;
    }

    .nav-list.right {
        float: right;
    }
</style>
<template>
    <Menu mode="horizontal" :theme="theme" :active-key="activeMenu">
        <div class="container">
            <img class="logo" src="../assets/logo.png" alt="Logo图片">
            <div class="nav-list">
                <Submenu v-for="(m, key) in menu" :key="key" :name="m.name" @click.native="headerLink(m.path, m.name)">
                    <template slot="title">
                        {{m.name}}
                    </template>
                    <MenuItem v-for="s in m.child" @click.native="routerLink(s.path)">
                        {{s.name}}
                    </MenuItem>
                </Submenu>
            </div>
            <div class="nav-list right">
                <router-link to="/login">
                    <MenuItem>
                        <Icon type="power" color="red"></Icon>
                        登出系统
                    </MenuItem>
                </router-link>
            </div>
        </div>
    </Menu>
</template>

<script>
  export default {
    data () {
      return {
        theme: 'light',
        menu: this.$router.options.modules,
        activeMenu: this.$router.currentRoute.meta.key
      }
    },
    methods: {
      routerLink (path) {
        this.$router.push(path)
      },
      headerLink (path, name) {
        this.activeMenu = name
        this.$router.push(path)
      }
    }
  }
</script>