<template>
    <section>
        <modal v-model="showModal" size="lg" :footer="false" @hide="callback" ref="modal" id="modal-demo">
            <div v-html="modalHtml"></div>
        </modal>
    </section>
</template>

<script>
    export default {
        name: "Modal1",
        data() {
            return {
                showModal: false,
                modalHtml: '',
            }
        },
        mounted() {
            this.$root.$on('open-model', data => {
                this.modalHtml = data.html;
                this.showModal = true;
            });
        }
    }
</script>

<style scoped>

</style>