<template>
    <div class="container" v-if="showModal">
        <!-- Modal -->
        <transition name="modal">
            <div class="modal-mask">
                <div class="modal-wrapper">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" @click="showModal=false">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h4 class="modal-title">Modal title</h4>
                            </div>
                            <div class="modal-body">
                                modal body
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </transition>
        <!--<div class="modal fade" id="searchModal" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Search result:</h4>
                    </div>
                    <div class="modal-body">
                        <p>Some text in the modal.</p>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>-->
    </div>
</template>

<script>
    export default {
        name: "Modal1",
        data() {
            return {
                showModal: false,
            }
        },
        mounted() {
            this.$root.$on('open-model', data => {
                this.showModal = true
            });
        }
    }
</script>

<style scoped>

</style>