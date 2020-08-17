var foot = new Vue({
    el: '#footer',
    data: {
        showGoTop: false,
        currentLocale: 'en',
        locales: [],
        langText: '',
        color: [],
    },
    methods: {
        getScroll: function () {
            if (window.scrollY > 100)
                this.showGoTop = true;
            else
                this.showGoTop = false;
        },
        goTop: function () {
            window.scrollTo(0, 0);
        },
        selectChanged: function () {
            axios.post('/post.changeLocale', {
                locale: this.currentLocale
            })
                .then(function (response) {
                    location.reload();
                })
                .catch(function (error) {
                    console.log(error);
                    location.reload();
                });
        }
    },
    mounted: function () {
        window.document.body.onscroll = this.getScroll;
        this.locales.push({
            'id': 'en',
            'name': 'English'
        });
        this.locales.push({
            'id': 'cht',
            'name': '繁體中文'
        });
        this.locales.push({
            'id': 'chs',
            'name': '简体中文'
        });
        var self = this;
        axios.get('/footer', {
            params: {
            }
        })
            .then(function (response) {
                self.currentLocale = response.data.locale;
                self.langText = response.data.text.language;
                self.color = response.data.color;
            })
            .catch(function (error) {
                console.log(error);
            });

    }
})