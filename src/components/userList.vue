<template>
    <div>
    	<Input v-model.trim="searchName" style="width:200px" placeholder="请输入姓名"/>
    	<Button type="primary" icon="ios-search" @click="handleSearch"></Button>
    	<Table :columns="columns" :data="userList" :loading="loading" @on-sort-change="sort"></Table>
    	<Page :total="total" :current="current" @on-change="changePage" show-total show-elevator></Page>
    </div>
</template>
<script>
  export default {
    data () {
      return {
        loading: false,
        current: 1,
        rowNum: 10,
        total: 0,
        userList: [],
        searchName: '',
        search: 0,
        sortColumn: '',
        order: '',
        columns: [
          {
            title: '真实姓名',
            key: 'real_name',
            sortable: 'custom'
          },
          {
            title: '手机号',
            key: 'phone'
          },
          {
            title: '登录帐号',
            key: 'username'
          },
          {
            title: '注册时间',
            key: 'reg_time',
            sortable: 'custom',
            render: (h, params) => {
              return h('span', this.toolUtil.formatDate(new Date(parseInt(params.row.reg_time * 1000))))
            }
          },
          {
            title: '总金额',
            key: 'reg_capital'
          },
          {
            title: '状态',
            key: 'state',
            render: (h, params) => {
              const color = params.row.state === '1' ? 'green' : params.row.state === '2' ? 'red' : 'gray'
              const text = params.row.state === '1' ? '正常' : params.row.state === '2' ? '锁定' : '不可用'
              return h('Tag', {props: {type: 'success', color: `${color}`}}, `${text}`)
            }
          },
          {
            title: '操作',
            key: 'action',
            render: (h, params) => {
              return h('div', [
                h('i-button', {
                  props: {
                    type: 'primary',
                    size: 'small'
                  },
                  style: {
                    marginRight: '10px'
                  },
                  on: {
                    click: () => {
                      this.showEdit(`${params.row.id}`)
                    }
                  }
                }, '查看'),
                h('i-button', {
                  props: {
                    type: 'error',
                    size: 'small'
                  },
                  on: {
                    click: () => {
                      this.delete(`${params.row.id}`)
                    }
                  }
                }, '删除')
              ])
            }
          }
        ]
      }
    },
    created () {
      this.getList('')
    },
    methods: {
      getList () {
        let self = this
        this.$http.post('api/index.php?c=user&a=userList', {
          'page': self.current,
          'rowNum': self.rowNum,
          'search': self.search,
          'searchName': self.searchName,
          'sortColumn': self.sortColumn,
          'order': self.order
        }).then(function (res) {
          this.userList = res.body.data.list
          this.total = res.body.data.total
          this.loading = false
        })
      },
      showEdit (index) {
        this.$router.push({name: '编辑用户', params: {uid: index}})
      },
      delete (index) {
        this.$http.post('api/index.php?c=user&a=delete', {'uid': index})
        .then(function (res) {
          if (res.body.state === '200') {
            this.$Message.success(res.body.title)
            this.getList()
          } else {
            this.$Message.error(res.body.title)
          }
        })
      },
      changePage (page) {
        this.current = page
        this.loading = true
        this.userList = this.getList()
      },
      handleSearch (event) {
        this.search = 1
        this.getList()
      },
      sort (data) {
        this.sortColumn = data.key
        this.order = data.order === 'normal' ? '' : data.order
        this.getList()
      }
    }
  }
</script>
