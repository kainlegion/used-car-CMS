<template>
  <Form ref="formValidate" :model="formValidate" :rules="ruleValidate" :label-width="80">
    <FormItem label="真实姓名" prop="name">
      <Input v-model.trim="formValidate.name" placeholder="" clearable></Input>
    </FormItem>
    <FormItem label="手机号" prop="phone">
      <Input v-model.trim="formValidate.phone" placeholder=""></Input>
    </FormItem>
    <FormItem label="总金额" prop="totalAmount">
      <Input v-model.trim="formValidate.totalAmount" clearable>
    </FormItem>
    <FormItem label="登录帐号" prop="account">
      <Input v-model.trim="formValidate.account" placeholder="" clearable></Input>
    </FormItem>
    <FormItem label="密码" prop="pwd">
      <Input v-model="formValidate.pwd" type="password" placeholder="" clearable></Input>
    </FormItem>
    <FormItem label="确认密码" prop="pwdCheck">
      <Input v-model="formValidate.pwdCheck" type="password" placeholder="" clearable></Input>
    </FormItem>
    <FormItem label="状态" prop="state">
      <RadioGroup v-model="formValidate.state">
        <Radio label="1">正常</Radio>
        <Radio label="2">锁定</Radio>
        <Radio label="3">不可用</Radio>
      </RadioGroup>
    </FormItem>
    <FormItem>
      <Input v-model="formValidate.uid" type="hidden"></Input>
      <Button type="primary" @click="handleSubmit('formValidate')">保存</Button>
      <Button type="ghost" @click="handleBack()" style="margin-left: 8px">用户列表</Button>
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
      const validateAccount = (rule, value, callback) => {
        if (!value) {
          return callback(new Error('帐号不能为空'))
        }
        this.$http.post('api/index.php?c=user&a=asyncCheck', {'username': value, 'uid': this.formValidate.uid}).then((res) => {
          let state = res.body.state
          if (state === '200') {
            callback()
          } else if (state === '201') {
            callback(new Error('帐号已存在'))
          } else {
            callback(new Error('帐号不能为空'))
          }
        })
      }
      const validatePhone = (rule, value, callback) => {
        if (value.length === 0) {
          callback(new Error('手机号不能为空'))
        } else if (isNaN(value)) {
          callback(new Error('手机号必需为数字'))
        } else if (value.length > 11) {
          callback(new Error('手机号长度不能大于11位'))
        } else {
          callback()
        }
      }
      return {
        formValidate: {
          uid: this.$route.params.uid,
          name: '',
          phone: '',
          totalAmount: '',
          account: '',
          pwd: '',
          pwdCheck: '',
          state: ''
        },
        ruleValidate: {
          name: [
            {required: true, message: '姓名不能为空', trigger: 'blur'}
          ],
          totalAmount: [
            {required: true, validator: validateNumber, trigger: 'blur'}
          ],
          account: [
            {required: true, validator: validateAccount, trigger: 'blur'}
          ],
          pwd: [
            {required: true, validator: validatePass, trigger: 'blur'}
          ],
          pwdCheck: [
            {required: true, validator: validatePassCheck, trigger: 'blur'}
          ],
          phone: [
            {required: true, validator: validatePhone, trigger: 'blur'}
          ],
          state: [
            {required: true, message: '请设置用户状态', trigger: 'change'}
          ]
        }
      }
    },
    created () {
      this.userInfo()
    },
    methods: {
      userInfo () {
        if (this.formValidate.uid) {
          this.$http.post('api/index.php?c=user&a=getUser', {'uid': this.formValidate.uid})
          .then(function (res) {
            let userInfo = res.body.data.userInfo
            this.formValidate.name = userInfo.real_name
            this.formValidate.totalAmount = userInfo.reg_capital
            this.formValidate.account = userInfo.username
            this.formValidate.pwd = userInfo.passwd
            this.formValidate.pwdCheck = userInfo.passwd
            this.formValidate.phone = userInfo.phone
            this.formValidate.state = userInfo.state
          })
        }
      },
      handleSubmit (name) {
        this.$refs[name].validate((valid) => {
          if (valid) {
            this.$http.post('api/index.php?c=user&a=edit', this.formValidate).then((responses) => {
              if (responses.body.state === '200') {
                this.$Message.success(responses.body.title)
                this.handleBack()
              } else {
                this.$Message.error(responses.body.title)
              }
            })
          } else {
            this.$Message.error('Fail!')
          }
        })
      },
      handleBack () {
        this.$router.push({path: '/user'})
      }
    }
  }
</script>
