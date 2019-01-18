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
        <FormItem label="发布状态" prop="release">
            <RadioGroup v-model="formValidate.release">
            	  <Radio label="1">发布</Radio>
                <Radio label="2">未发布</Radio>
            </RadioGroup>
        </FormItem>
        <FormItem label="车辆图片" prop="picture">
  	        <div class="demo-upload-list" v-for="item in formValidate.uploadList">
    		        <template v-if="item.status === 'finished'">
    		            <img :src="'/photo/' + formValidate.cid + '/' + item.name">
    		            <div class="demo-upload-list-cover">
    		                <Icon type="ios-eye-outline" @click.native="handleView(item.name)"></Icon>
    		                <Icon type="ios-trash-outline" @click.native="handleRemove(item)" v-if="userType !== 2"></Icon>
    		            </div>
    		        </template>
    		        <template v-else>
    		            <Progress v-if="item.showProgress" :percent="item.percentage" hide-info></Progress>
    		        </template>
    		    </div>
    		    <Upload
                v-if="userType !== 2"
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
    		        :action="uploadUrl"
    		        style="display: inline-block;width:58px;">
    		        <div style="width: 58px;height:58px;line-height: 58px;">
    		            <Icon type="ios-camera" size="20"></Icon>
    		        </div>
    		    </Upload>
    		    <Modal title="View Image" v-model="visible">
    		        <img :src="'/photo/' + formValidate.cid + '/' + imgName" v-if="visible" style="width: 100%">
    		    </Modal>
    		</FormItem>
        <FormItem label="描述" prop="desc">
            <Input v-model="formValidate.desc" type="textarea" :autosize="{minRows: 2,maxRows: 5}"
                   placeholder="Enter something..."></Input>
        </FormItem>
        <FormItem>
            <Button type="primary" @click="handleSubmit('formValidate')"  v-if="userType !== 2">保存</Button>
            <Button type="ghost" @click="handleBack()" style="margin-left: 8px">车辆列表</Button>
        </FormItem>
    </Form>
</template>
<script>
  export default {
    data () {
      return {
        userType: 0,
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
        checkedInvestor: [],
        emissionGradeList: [],
        formValidate: {
          cid: this.$route.params.cid,
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
          proportion: 0,
          uploadList: [],
          release: '2'
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
      this.getUserType()
      this.carInfo()
      this.getEmissionGradeList()
      this.getInvestorList()
    },
    created () {

    },
    computed: {
      uploadUrl () {
        return 'api/index.php?c=car&a=uploadPhoto&cid=' + this.formValidate.cid
      }
    },
    methods: {
      carInfo () {
        if (this.formValidate.cid) {
          this.$http.post('api/index.php?c=car&a=getCar', {'cid': this.formValidate.cid})
          .then(function (res) {
            let carInfo = res.body.data.carInfo
            this.formValidate.brand = carInfo.brand
            this.formValidate.model = carInfo.model
            this.formValidate.year = carInfo.particular_year
            this.formValidate.date = carInfo.particular_year
            this.formValidate.emissionGrade = carInfo.emission_grade
            this.formValidate.state = carInfo.state
            this.formValidate.desc = carInfo.description
            this.formValidate.purchasePrice = carInfo.purchase_price
            this.formValidate.setupCost = carInfo.setup_cost
            this.formValidate.investment = carInfo.investment
            this.formValidate.selfFunds = carInfo.self_funds
            this.formValidate.salePrice = carInfo.sale_price
            this.formValidate.proportion = carInfo.proportion
            this.formValidate.release = carInfo.release
            this.$refs.upload.fileList = res.body.data.photo
            this.formValidate.uploadList = this.$refs.upload.fileList
            this.checkedInvestor = res.body.data.investor
          })
        }
      },
      handleSubmit (name) {
        this.$refs[name].validate((valid) => {
          if (valid) {
            this.$http.post('api/index.php?c=car&a=edit', this.formValidate).then((responses) => {
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
        this.$router.push({path: '/car'})
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
          for (let i in res.body.data.list) {
            if (res.body.data.list[i].id in this.checkedInvestor) {
              res.body.data.list[i]._checked = true
            }
          }
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
        this.$http.post('api/index.php?c=car&a=deletePhoto', {'cid': this.formValidate.cid, 'fileName': file.name}).then((res) => {
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
      },
      getUserType () {
        this.$http.post('api/index.php?c=auth&a=getUserType').then(function (res) {
          this.userType = parseInt(res.data.userType)
        })
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
