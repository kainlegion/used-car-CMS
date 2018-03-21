<style>
    .login-card {
        width: 400px;
        margin: 100px auto;
    }
</style>
<template>
    <Card class="login-card">
        <p slot="title">{{sysName}} 登录</p>
        <Form ref="formValidate" :model="formValidate" :rules="ruleValidate" label-position="top">
            <FormItem label="username" prop="username">
                <Input v-model="formValidate.username" placeholder="Enter your username"></Input>
            </FormItem>
            <FormItem label="password" prop="password">
                <Input v-model="formValidate.password" type="password" placeholder="Enter your password"></Input>
            </FormItem>
            <FormItem>
                <Button type="primary" long size="large" @click="handleSubmit('formValidate')">登录</Button>
            </FormItem>
        </Form>
    </Card>
</template>
<script>
  export default {
    props: ['sysName'],
    data () {
      return {
        formValidate: {
          username: '',
          password: ''
        },
        ruleValidate: {
          username: [
            {required: true, message: '用户名不能为空', trigger: 'blur'}
          ],
          password: [
            {required: true, message: '密码不能为空', trigger: 'blur'}
          ]
        }
      }
    },
    methods: {
      handleSubmit (name) {
        this.$refs[name].validate((valid) => {
          if (valid) {
            this.$http.post('api/index.php?c=auth&a=signin', this.formValidate).then((responses) => {
              this.$router.push({path: '/'})
            })
          }
        })
      }
    }
  }
</script>
