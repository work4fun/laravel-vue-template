<script>
    import { Popconfirm } from 'element-ui'

    export default {
        extends: Popconfirm,
        methods: {
            confirm() {
                this.visible = false;
                this.$emit('on-confirm');
            }
        },
        mounted () {

        }
    }
</script>

<style scoped>

</style>
