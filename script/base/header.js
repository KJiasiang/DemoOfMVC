var header = new Vue({
    el: '#head_bar',
    data: {
        title: '',
        color: {},
        visible: false,
        page: 0,
        text: {},
        menu: [],
        auth: 0,
        alias: '',
    },
    methods: {
        open: function () {
            this.visible = true
        },
        close: function () {
            this.visible = false
        },
        verifySelected: function (index) {
            if (index == this.page)
                return this.color.selected;
            else
                return this.color.unselect
        },
        addMenu: function (text, value, index, target) {
            var classValue = value
            if (index != 0)
                classValue += this.verifySelected(index);

            this.menu.push({
                text: text,
                class: classValue,
                target: target,
            })
        },
        menuClick: function (target) {
            location = target
        },
    },
    mounted: function () {
        var self = this;
        self.page = page;
        self.title = title;
        axios.get('/header', {
            params: {
            }
        }).then(function (response) {
            self.color = response.data.color;
            self.text = response.data.text;

            var class1 = 'w3bar-item w3button w3xlarge ';
            var class2 = 'w3bar-item w3xlarge w3wide w3border';
            var class3 = 'w3bar-item w3xlarge w3wide w3topbar w3button ';
            self.addMenu(self.text.overview, class1, 1, '/overview');
            var body = document.body;
            body.classList.add(self.color.background2);

            self.auth = response.data.auth;
        }).catch(function (error) {
            console.log(error);
        });
    },
})
var notify = new Vue({
    el: '#notify',
    data: {
        visible: false,
    },
})