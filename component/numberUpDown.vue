<template>
    <input
    ref="item"
    :value="value"
    type="number"
    class="w3input"
    :min="min"
    :max="max"
    :placeholder="min+'~'+max"
    @keydown.enter='$emit("enter")'
    @keydown.esc='$emit("esc")'
    @change="change"
  />
</template>

<script>
module.exports = {
  model: {
    prop: "value",
    event: "change"
  },
  props: {
    value: [String, Number],
    min: [String, Number],
    max: [String, Number],
    default: [String, Number],
    usefloat: [String, Boolean]
  },
  methods: {
   change: function(e) {
     if(this.usefloat){
        var val = parseFloat(this.$refs.item.value);
        var min = parseFloat(this.min);
        var max = parseFloat(this.max);
        if (isNaN(val)) val = this.default;
        if (val < min) val = min;
        if (val > max) val = max;
        this.$refs.item.value = val;
        this.$emit("change", parseFloat(val));
     }else{
        var val = parseInt(this.$refs.item.value);
        var min = parseInt(this.min);
        var max = parseInt(this.max);
        if (isNaN(val)) val = this.default;
        if (val < min) val = min;
        if (val > max) val = max;
        this.$refs.item.value = val;
        this.$emit("change", parseInt(val));
     }
     
    },
    select: function() {
      this.$refs.item.select();
    }
  },
  mounted: function() {
  }
};
</script>

<style>
</style>
