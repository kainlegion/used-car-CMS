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
        <FormItem label="出售价格" prop="salePrice">
            <InputNumber
            v-model="formValidate.salePrice"
            style="width:auto"
            :formatter="value => `${value}`.replace(/B(?=(d{3})+(?!d))/g, ',')"
            :parser="value => value.replace(/$s?|(,*)/g, '')"></InputNumber>
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
            :parser="value => value.replace('%', '')"></InputNumber> %
        </FormItem>
        <FormItem label="车辆状态" prop="state">
            <RadioGroup v-model="formValidate.state">
            	<Radio label="1">整备</Radio>
                <Radio label="2">在售</Radio>
                <Radio label="3">已售</Radio>
            </RadioGroup>
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
          salePrice: 0,
          numOfInvestment: 0,
          proportion: 0
        },
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
    created () {
      this.getEmissionGradeList()
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
      }
    }
  }
</script>
