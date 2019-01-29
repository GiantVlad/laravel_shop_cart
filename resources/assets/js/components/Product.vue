<template>
    <div class="col-lg-4 col-sm-6 product-cart">
        <div class="cart-wrapper">
            <div class="cart-header">
                <a :href="baseUrl+'/shop/' + product.id">
                    <h4 class="header">{{product.name}}</h4>
                </a>
                <p>Category: {{product.catalogs.name}}</p>
            </div>
            <div class="effect" @click="slide">
                <span class="glyphicon glyphicon-triangle-bottom" :class="{'up':!active}"></span>
            </div>

            <div class="thumbnail">
                <a :href="baseUrl+'/shop/' + product.id">
                    <div class="img-wrapper">
                        <vue-slide-up-down :active="active">
                        <img class="center-block" :alt="'product id '+product.id"
                             :src="baseUrl+'/images/'+product.image">
                        </vue-slide-up-down>
                        <vue-slide-up-down :active="!active">
                            <p class="product-shop-desc">Description:<br>{{product.description}}</p>
                        </vue-slide-up-down>
                    </div>
                </a>
            </div>
            <p>Price: {{product.price}}
                <button type="button" class="btn btn-link" :name="'addFromShop'+product.id" :value="product.id">
                    ADD TO CART
                </button>
            </p>
        </div>
    </div>

</template>

<script>
    import VueSlideUpDown from 'vue-slide-up-down'
    export default {
        name: 'Product',
        components: {VueSlideUpDown},
        props: ['product'],
        data() {
            return {
                baseUrl: '',
                csrf: '',
                active: true
            }
        },
        computed: {

        },
        methods: {
            slide () {
                this.active = !this.active
            },
        },
        mounted() {
            this.baseUrl = window.location.origin;
            this.csrf = document.head.querySelector('meta[name="csrf-token"]').content;
        }
    }
</script>

<style lang="scss" scoped>
    .product-cart {
        margin-bottom: 15px;

        .cart-wrapper {
            border: 1px solid gray;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            text-align: center;

            .cart-header {
                height: 6em;
                position: relative;
            }
            .effect {
                margin-top: 5px;
                .glyphicon {
                    vertical-align: top;
                    -webkit-transition: 0.6s ease-out;
                    -moz-transition: 0.6s ease-out;
                    transition: 0.6s ease-out;
                }
                .glyphicon.up {
                    -webkit-transform: rotateZ(180deg);
                    -moz-transform: rotateZ(180deg);
                    transform: rotateZ(180deg);
                }
                .glyphicon-triangle-bottom {
                    cursor: pointer;
                }

            }
            .thumbnail{
                a:hover {
                    text-decoration: none;
                }
                .img-wrapper {
                    width: 100%;
                    padding-bottom: 100%; /* your aspect ratio here! */
                    position: relative;
                    height: 20em;

                    .product-shop-desc {
                        text-align: justify;
                        padding: 0px 7px;
                    }
                    img {
                        position: relative;
                        top: 0;
                        bottom: 0;
                        left: 0;
                        right: 0;
                        max-width: 100%;
                    }
                }
            }
        }
    }
</style>
