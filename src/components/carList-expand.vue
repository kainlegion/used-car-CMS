<style scoped>
    .expand-row{
        margin-bottom: 16px
    }
</style>
<template>
    <div>
        <Row class="expand-row">
            <Col span="8">
                <span class="expand-key">上牌年份: </span>
                <span class="expand-value">{{ row.particular_year }}</span>
            </Col>
            <Col span="8">
                <span class="expand-key">排放标准: </span>
                <span class="expand-value">{{ row.emission_grade }}</span>
            </Col>
            <Col span="8">
                <span class="expand-key">收车价格: </span>
                <span class="expand-value">{{ row.purchase_price }}</span>
            </Col>
        </Row>
        <Row class="expand-row">
            <Col span="8">
                <span class="expand-key">整备费用: </span>
                <span class="expand-value">{{ row.setup_cost }}</span>
            </Col>
            <Col span="8">
                <span class="expand-key">投资金额: </span>
                <span class="expand-value">{{ row.investment }}</span>
            </Col>
            <Col span="8">
                <span class="expand-key">出售价格: </span>
                <span class="expand-value">{{ row.sale_price }}</span>
            </Col>
        </Row>
        <Row>
        	<Col span="8">
                <span class="expand-key">投资人数: </span>
                <span class="expand-value">{{ row.num_of_investor }}</span>
            </Col>
            <Col span="8">
                <span class="expand-key">分成比例: </span>
                <span class="expand-value">{{ row.proportion }}%</span>
            </Col>
            <Col span="8">
                <span class="expand-key">利润: </span>
                <span class="expand-value">{{ row.profit }}</span>
            </Col>
        </Row>
    </div>
</template>
<script>
  export default {
    props: {
      row: Object
    }
  }
</script>
