<template>
  <Form ref="formValidate" :model="formValidate" :rules="ruleValidate" :label-width="80">
    <FormItem label="姓名" prop="name">
      <Input v-model="formValidate.name" placeholder=""></Input>
    </FormItem>
    <FormItem label="总金额" prop="totalAmount">
      <Input v-model="formValidate.totalAmount">
    </FormItem>
    <FormItem label="帐号" prop="account">
      <Input v-model="formValidate.account" placeholder=""></Input>
    </FormItem>
    <FormItem label="密码" prop="pwd">
      <Input v-model="formValidate.pwd" type="password" placeholder=""></Input>
    </FormItem>
    <FormItem label="确认密码" prop="pwdCheck">
      <Input v-model="formValidate.pwdCheck" type="password" placeholder=""></Input>
    </FormItem>
    <FormItem>
      <Button type="primary" @click="handleSubmit('formValidate')">添加</Button>
      <Button type="ghost" @click="handleReset('formValidate')" style="margin-left: 8px">重置</Button>
    </FormItem>
  </Form>
</template>
<script>
  export default {
    data () {
      const validateNumber = (rule, value, callback) => {
        if (value === '') {
          callback(new Error('总金额不能为空'))
        } else if (isNaN(value)) {
          callback(new Error('总金额必需为数字'))
        } else {
          callback()
        }
      }
      const validatePass = (rule, value, callback) => {
        if (value === '') {
          callback(new Error('密码不能为空'))
        } else {
          if (this.formValidate.pwdCheck !== '') {
            // 对第二个密码框单独验证
            this.$refs.formValidate.validateField('pwdCheck')
          }
          callback()
        }
      }
      const validatePassCheck = (rule, value, callback) => {
        if (value === '') {
          callback(new Error('请确认密码'))
        } else if (value !== this.formValidate.pwd) {
          callback(new Error('两次密码不一致'))
        } else {
          callback()
        }
      }
      return {
        formValidate: {
          name: '',
          totalAmount: '',
          account: '',
          pwd: '',
          pwdCheck: ''
        },
        ruleValidate: {
          name: [
            {required: true, message: '姓名不能为空', trigger: 'blur'}
          ],
          totalAmount: [
            {required: true, validator: validateNumber, trigger: 'blur'}
          ],
          account: [
            {required: true, message: '帐号不能为空', trigger: 'blur'}
          ],
          pwd: [
            {required: true, validator: validatePass, trigger: 'blur'}
          ],
          pwdCheck: [
            {required: true, validator: validatePassCheck, trigger: 'blur'}
          ]
        }
      }
    },
    methods: {
      handleSubmit (name) {
        this.$refs[name].validate((valid) => {
          if (valid) {
            this.$Message.success('Success!')
          } else {
            this.$Message.error('Fail!')
          }
        })
      },
      handleReset (name) {
        this.$refs[name].resetFields()
      }
    }
  }
</script>
