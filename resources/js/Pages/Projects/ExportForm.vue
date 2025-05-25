<!-- <template>
    <form @submit.prevent="exportExcel">
        <label v-for="f in fields" :key="f">
            <input type="checkbox" v-model="selectedFields" :value="f" />
            {{ f }}
        </label>
        <button type="submit">Export</button>
    </form>
</template>
<script setup>
import { ref } from "vue";
const props = defineProps({ fields: Array, entity: String });
const selectedFields = ref([...props.fields]);
function exportExcel() {
    window.location = `/${
        props.entity
    }/export?fields=${selectedFields.value.join(",")}`;
}
</script> -->

<script setup>
const props = defineProps({ fields: Array, entity: String });
const emit = defineEmits(["exported"]);
import { ref } from "vue";
const selectedFields = ref([...props.fields]);

function exportExcel() {
    fetch(`/${props.entity}/export?fields=${selectedFields.value.join(",")}`)
        .then((res) => res.json())
        .then((data) => emit("exported", data.job_id)); // Panggil emit supaya parent (Index.vue) fetch ulang daftar export
}
</script>
<template>
    <form @submit.prevent="exportExcel">
        <div
            v-for="f in fields"
            :key="f"
            style="display: inline-block; margin-right: 12px"
        >
            <label>
                <input type="checkbox" v-model="selectedFields" :value="f" />
                {{ f }}
            </label>
        </div>
        <button type="submit">Export</button>
    </form>
</template>
