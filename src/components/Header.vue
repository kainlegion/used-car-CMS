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
                <Submenu v-for="(m, key) in dealMenu" :key="key" :name="m.name" @click.native="headerLink(m.path, m.name)" v-if="m.name !== ''">
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
        userType: 0,
        theme: 'light',
        menu: this.$router.options.modules,
        activeMenu: this.$router.currentRoute.meta.key
      }
    },
    computed: {
      dealMenu: function () {
        this.getUserType()
        // for (let i in this.menu) {
          // if (this.userType === 2) {
            // this.menu.splice(i, 1)
          // }
        // }
        // return this.menu
        if (this.userType === 1) {
          return this.menu
        }
      }
    },
    methods: {
      routerLink (path) {
        this.$router.push(path)
      },
      headerLink (path, name) {
        this.activeMenu = name
        this.$router.push(path)
      },
      getUserType () {
        this.$http.post('api/index.php?c=auth&a=getUserType').then(function (res) {
          this.userType = parseInt(res.data.userType)
        })
      }
    }
  }
</script>
