Vue.use(httpVueLoader);
new Vue({
    el: '#app',
    data: {
        color: ['black', 'red', 'green'],
    },
    components: {
        'numberinput': 'url:/component?a=numberUpDown',
    },
    methods: {

    },
    mounted: function () {
    },
});