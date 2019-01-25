<template>
    <Checkbox-group v-model="tableColumnsChecked" @on-change="changeTableColumns">
        <Checkbox label="show">Show</Checkbox>
        <Checkbox label="weak">Weak</Checkbox>
        <Checkbox label="signin">Signin</Checkbox>
        <Checkbox label="click">Click</Checkbox>
        <Checkbox label="active">Active</Checkbox>
        <Checkbox label="day7">7, retained</Checkbox>
        <Checkbox label="day30">30, retained</Checkbox>
        <Checkbox label="tomorrow">The next day left</Checkbox>
        <Checkbox label="day">Day Active</Checkbox>
        <Checkbox label="week">Week Active</Checkbox>
        <Checkbox label="month">Month Active</Checkbox>
    </Checkbox-group>
    <div>
    	<Select v-model="searchColumn" style="width:100px">
        	<Option v-for="(item, key) in columnList" :value="key" :key="key">{{ item }}</Option>
    	</Select>
    	<Input v-model.trim="searchName" clearable style="width:200px" placeholder="请输入关键字"/>
    	<Button type="primary" icon="ios-search" @click="handleSearch"></Button>
    	<Table :columns="columns" :data="carList" :loading="loading" @on-sort-change="sort"></Table>
    	<Page :total="total" :current="current" @on-change="changePage" show-total show-elevator></Page>
    </div>
</template>
<script>
  import expandRow from './carList-expand.vue'
  export default {
    components: { expandRow },
    data () {
      return {
        tableColumnsChecked: ['show', 'weak', 'signin', 'click', 'active', 'day7', 'day30', 'tomorrow', 'day', 'week', 'month'],
        userType: 0,
        loading: false,
        current: 1,
        rowNum: 10,
        total: 0,
        carList: [],
        searchColumn: 'brand',
        searchName: '',
        search: 0,
        searchState: [],
        searchRelease: [],
        sortColumn: '',
        order: '',
        columnList: {
          'brand': '品牌',
          'model': '型号'
        },
        columns: [
          {
            type: 'expand',
            width: 50,
            render: (h, params) => {
              return h(expandRow, {
                props: {
                  row: params.row
                }
              })
            }
          },
          {
            title: '车辆照片',
            key: 'thumbnail',
            render: (h, params) => {
              if (params.row.file_name) {
                return h('div', [
                  h('img', {
                    attrs: {
                      src: '/photo/' + params.row.id + '/' + params.row.file_name
                    },
                    style: {
                      width: '40px',
                      height: '40px'
                    }
                  })
                ])
              }
            }
          },
          {
            title: '品牌',
            key: 'brand',
            sortable: 'custom'
          },
          {
            title: '型号',
            key: 'model'
          },
          {
            title: '上线时间',
            key: 'reg_time',
            sortable: 'custom',
            render: (h, params) => {
              return h('span', this.toolUtil.formatDate(new Date(parseInt(params.row.reg_time * 1000))))
            }
          },
          {
            title: '车辆状态',
            key: 'state',
            filters: [
              {
                label: '整备',
                value: 1
              },
              {
                label: '在售',
                value: 2
              },
              {
                label: '已售',
                value: 3
              }
            ],
            filterMultiple: false,
            filterRemote: this.filterState,
            render: (h, params) => {
              const color = params.row.state === '1' ? 'yellow' : params.row.state === '2' ? 'blue' : 'gray'
              const text = params.row.state === '1' ? '整备' : params.row.state === '2' ? '在售' : '已售'
              return h('Tag', {props: {type: 'success', color: `${color}`}}, `${text}`)
            }
          },
          {
            title: '发布状态',
            key: 'release',
            filters: [
              {
                label: '发布',
                value: 1
              },
              {
                label: '未发布',
                value: 2
              }
            ],
            filterMultiple: false,
            filterRemote: this.filterRelease,
            render: (h, params) => {
              const color = params.row.release === '1' ? 'green' : 'gray'
              const text = params.row.release === '1' ? '发布' : '未发布'
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
                  style: {
                    display: this.buttonDisplay()
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
      this.getUserType()
      this.getList('')
    },
    methods: {
      getList () {
        let self = this
        this.$http.post('api/index.php?c=car&a=carList', {
          'page': self.current,
          'rowNum': self.rowNum,
          'search': self.search,
          'searchColumn': self.searchColumn,
          'searchName': self.searchName,
          'searchState': self.searchState,
          'searchRelease': self.searchRelease,
          'sortColumn': self.sortColumn,
          'order': self.order
        }).then(function (res) {
          this.carList = res.body.data.list
          this.total = res.body.data.total
          this.loading = false
        })
      },
      showEdit (index) {
        this.$router.push({name: '编辑车辆', params: {cid: index}})
      },
      delete (index) {
        this.$http.post('api/index.php?c=car&a=delete', {'cid': index})
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
        this.carList = this.getList()
      },
      handleSearch (event) {
        this.search = 1
        this.getList()
      },
      sort (data) {
        this.sortColumn = data.key
        this.order = data.order === 'normal' ? '' : data.order
        this.getList()
      },
      filterState (value, row) {
        this.searchState = value
        this.getList()
      },
      filterRelease (value, row) {
        this.searchRelease = value
        this.getList()
      },
      getUserType () {
        this.$http.post('api/index.php?c=auth&a=getUserType').then(function (res) {
          this.userType = parseInt(res.data.userType)
        })
      },
      buttonDisplay () {
        return (this.userType === 2) ? 'none' : 'inline-block'
      }
    }
  }
</script>
