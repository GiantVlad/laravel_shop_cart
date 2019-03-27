<template>
    <div>
    <div class="col-sm-4 col-md-5 col-lg-4 general-nav-col">
        <form role="search" class="navbar-form">
            <div class="input-group col-xs-12">
                <input type="text" class="form-control" placeholder="Search" id="nav-search"
                    v-model="keyword">
                <div class="input-group-btn">
                    <button class="btn btn-default" type="button" id="nav-search-btn"
                            @click.prevent="search">
                        <i class="glyphicon glyphicon-search"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
    </div>
</template>

<script>
    import axios from 'axios'
    export default {
        name: "NavSearch",
        props:['searchUrl'],
        data() {
            return {
                keyword: '',
            }
        },
        methods: {
            search() {
                axios.post(this.searchUrl, {
                    keyword: this.keyword,
                    _token: this.csrf
                }).then(response => {
                    this.$root.$emit('open-model', response.data)
                }).catch(e => {
                    console.log(e)
                    //this.errors.push(e)
                })
            }
        },
        mounted() {
            //console.log(this.baseUrl)
        }
    }
</script>

<style scoped>

</style>