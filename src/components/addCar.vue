<template>
    <Form ref="formValidate" :model="formValidate" :rules="ruleValidate" :label-width="80">
        <FormItem label="品牌" prop="brand">
            <Input v-model="formValidate.brand" placeholder=""></Input>
        </FormItem>
        <FormItem label="型号" prop="model">
            <Input v-model="formValidate.model" placeholder=""></Input>
        </FormItem>
        <FormItem label="上牌年份" prop="year">
    		<DatePicker type="date" placeholder="Select date" v-model="formValidate.year" @on-change="selectDate"></DatePicker>
        </FormItem>
        <FormItem label="排放标准" prop="emissionGrade">
            <Select v-model="formValidate.emissionGrade" placeholder="" style="width:100px">
                <Option v-for="(grade, key) in emissionGradeList" :key="key" :value="key">{{ grade }}</Option>
            </Select>
        </FormItem>
        <FormItem label="收车价格" prop="purchasePrice">
            <InputNumber
            v-model="formValidate.purchasePrice"
            style="width:auto"
            :formatter="value => `${value}`.replace(/B(?=(d{3})+(?!d))/g, ',')"
            :parser="value => value.replace(/$s?|(,*)/g, '')"></InputNumber>
        </FormItem>
        <FormItem label="整备费用" prop="setupCost">
            <InputNumber
            v-model="formValidate.setupCost"
            style="width:auto"
            :formatter="value => `${value}`.replace(/B(?=(d{3})+(?!d))/g, ',')"
            :parser="value => value.replace(/$s?|(,*)/g, '')"></InputNumber>
        </FormItem>
        <FormItem label="投资金额" prop="investment">
            <InputNumber
            v-model="formValidate.investment"
            style="width:auto"
            :formatter="value => `${value}`.replace(/B(?=(d{3})+(?!d))/g, ',')"
            :parser="value => value.replace(/$s?|(,*)/g, '')"></InputNumber>
        </FormItem>
        <FormItem label="自有资金" prop="selfFunds">
            <InputNumber
            v-model="formValidate.selfFunds"
            style="width:auto"
            :formatter="value => `${value}`.replace(/B(?=(d{3})+(?!d))/g, ',')"
            :parser="value => value.replace(/$s?|(,*)/g, '')"></InputNumber>
        </FormItem>
        <FormItem label="出售价格" prop="salePrice">
            <InputNumber
            v-model="formValidate.salePrice"
            style="width:auto"
            :formatter="value => `${value}`.replace(/B(?=(d{3})+(?!d))/g, ',')"
            :parser="value => value.replace(/$s?|(,*)/g, '')"></InputNumber>
        </FormItem>
        <FormItem label="投资人" prop="investor">
            <div>
                <Table border height="300" ref="selection" :columns="columns" :data="investorList" @on-selection-change="handleSelectionInvestor"></Table>
            </div>
        </FormItem>
        <FormItem label="投资人数" prop="numOfInvestment">
            <InputNumber
            :min="0"
            v-model="formValidate.numOfInvestment"
            style="width:auto"
            :formatter="value => `${value}`.replace(/B(?=(d{3})+(?!d))/g, ',')"
            :parser="value => value.replace(/$s?|(,*)/g, '')"></InputNumber>
        </FormItem>
        <FormItem label="分成比例" prop="proportion">
            <InputNumber
            :max="100"
            :min="0"
            v-model="formValidate.proportion"
            style="width:auto"
            :formatter="value => `${value}%`"
            :parser="value => value.replace('%', '')"></InputNumber>
        </FormItem>
        <FormItem label="车辆状态" prop="state">
            <RadioGroup v-model="formValidate.state">
            	<Radio label="1">整备</Radio>
                <Radio label="2">在售</Radio>
                <Radio label="3">已售</Radio>
            </RadioGroup>
        </FormItem>
        <FormItem label="车辆图片" prop="picture">
	        <div class="demo-upload-list" v-for="item in formValidate.uploadList">
		        <template v-if="item.status === 'finished'">
		            <img :src="'/photo/' + item.name">
		            <div class="demo-upload-list-cover">
		                <Icon type="ios-eye-outline" @click.native="handleView(item.name)"></Icon>
		                <Icon type="ios-trash-outline" @click.native="handleRemove(item)"></Icon>
		            </div>
		        </template>
		        <template v-else>
		            <Progress v-if="item.showProgress" :percent="item.percentage" hide-info></Progress>
		        </template>
		    </div>
		    <Upload
		        ref="upload"
		        :show-upload-list="false"
		        :default-file-list="defaultList"
		        :on-success="handleSuccess"
		        :format="['jpg','jpeg','png']"
		        :max-size="2048"
		        :on-format-error="handleFormatError"
		        :on-exceeded-size="handleMaxSize"
		        :before-upload="handleBeforeUpload"
		        multiple
		        type="drag"
		        action="api/index.php?c=car&a=uploadPhoto"
		        style="display: inline-block;width:58px;">
		        <div style="width: 58px;height:58px;line-height: 58px;">
		            <Icon type="ios-camera" size="20"></Icon>
		        </div>
		    </Upload>
		    <Modal title="View Image" v-model="visible">
		        <img :src="'/photo/' + imgName" v-if="visible" style="width: 100%">
		    </Modal>
		</FormItem>
        <FormItem label="描述" prop="desc">
            <Input v-model="formValidate.desc" type="textarea" :autosize="{minRows: 2,maxRows: 5}"
                   placeholder="Enter something..."></Input>
        </FormItem>
        <FormItem>
            <Button type="primary" @click="handleSubmit('formValidate')">保存</Button>
            <Button type="ghost" @click="handleReset('formValidate')" style="margin-left: 8px">重置</Button>
        </FormItem>
    </Form>
</template>
<script>
  export default {
    data () {
      return {
        columns: [
          {
            type: 'selection',
            width: 60,
            align: 'center'
          },
          {
            title: '真实姓名',
            key: 'real_name'
          },
          {
            title: '手机号',
            key: 'phone'
          },
          {
            title: '登录帐号',
            key: 'username'
          }
        ],
        investorList: [],
        emissionGradeList: [],
        formValidate: {
          brand: '',
          model: '',
          date: '',
          year: '',
          emissionGrade: '',
          state: '',
          desc: '',
          purchasePrice: 0,
          setupCost: 0,
          investment: 0,
          investor: [],
          selfFunds: 0,
          salePrice: 0,
          numOfInvestment: 0,
          proportion: 0,
          uploadList: []
        },
        imgName: '',
        visible: false,
        defaultList: [],
        ruleValidate: {
          brand: [
            {required: true, message: 'The brand cannot be empty', trigger: 'blur'}
          ],
          model: [
            {required: true, message: 'The model cannot be empty', trigger: 'blur'}
          ],
          emissionGrade: [
            {required: true, message: 'Please select the grade', trigger: 'change'}
          ],
          year: [
            {required: true, type: 'date', message: 'Please select the date', trigger: 'change'}
          ],
          state: [
            {required: true, message: 'Please select state', trigger: 'change'}
          ]
        }
      }
    },
    mounted () {
      this.formValidate.uploadList = this.$refs.upload.fileList
    },
    created () {
      this.getEmissionGradeList()
      this.getInvestorList()
    },
    methods: {
      handleSubmit (name) {
        this.$refs[name].validate((valid) => {
          if (valid) {
            this.$http.post('api/index.php?c=car&a=add', this.formValidate).then((responses) => {
              if (responses.body.state === '200') {
                this.$Message.success(responses.body.title)
                this.$refs[name].resetFields('formValidate')
              } else {
                this.$Message.error(responses.body.title)
              }
            })
          } else {
            this.$Message.error('Fail!')
          }
        })
      },
      handleReset (name) {
        this.$refs[name].resetFields()
      },
      selectDate (year) {
        this.formValidate.date = year
      },
      getEmissionGradeList () {
        this.$http.post('api/index.php?c=car&a=getEmissionGradeList').then((res) => {
          this.emissionGradeList = res.body
        })
      },
      getInvestorList () {
        this.$http.post('api/index.php?c=user&a=userList').then((res) => {
          this.investorList = res.body.data.list
        })
      },
      handleSelectionInvestor (selection) {
        this.formValidate.investor = selection
      },
      handleView (name) {
        this.imgName = name
        this.visible = true
      },
      handleRemove (file) {
        this.$http.post('api/index.php?c=car&a=deletePhoto', {'fileName': file.name}).then((res) => {
          if (res.body.state === '200') {
            this.$Message.success(res.body.title)
            const fileList = this.$refs.upload.fileList
            this.$refs.upload.fileList.splice(fileList.indexOf(file), 1)
          } else {
            this.$Message.error(res.body.title)
          }
        })
      },
      handleSuccess (res, file) {
      },
      handleFormatError (file) {
        this.$Notice.warning({
          title: '文件格式不正确',
          desc: '文件 ' + file.name + ' 的格式不正确, 请上传 jpg或 png。'
        })
      },
      handleMaxSize (file) {
        this.$Notice.warning({
          title: '超过文件大小限制',
          desc: '图片  ' + file.name + ' 太大, 文件应小于2M.'
        })
      },
      handleBeforeUpload () {
        const check = this.formValidate.uploadList.length < 5
        if (!check) {
          this.$Notice.warning({
            title: '最多上传5张照片'
          })
        }
        return check
      }
    }
  }
</script>
<style>
    .demo-upload-list{
        display: inline-block;
        width: 60px;
        height: 60px;
        text-align: center;
        line-height: 60px;
        border: 1px solid transparent;
        border-radius: 4px;
        overflow: hidden;
        background: #fff;
        position: relative;
        box-shadow: 0 1px 1px rgba(0,0,0,.2);
        margin-right: 4px;
    }
    .demo-upload-list img{
        width: 100%;
        height: 100%;
    }
    .demo-upload-list-cover{
        display: none;
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(0,0,0,.6);
    }
    .demo-upload-list:hover .demo-upload-list-cover{
        display: block;
    }
    .demo-upload-list-cover i{
        color: #fff;
        font-size: 20px;
        cursor: pointer;
        margin: 0 2px;
    }
</style>
