<template>
    <form @submit.prevent="submitImport" enctype="multipart/form-data">
        <input type="file" @change="onFileChange" required />
        <div v-if="headers.length">
            <div v-for="header in headers" :key="header">
                {{ header }}:
                <select v-model="mapping[header]">
                    <option value="">---</option>
                    <option v-for="f in dbFields" :key="f" :value="f">
                        {{ f }}
                    </option>
                </select>
            </div>
            <button type="submit">Import</button>
        </div>
    </form>
</template>

<script setup>
import { ref } from "vue";
const props = defineProps({ dbFields: Array, entity: String });

const headers = ref([]); // produksi: ambil header dari file excel
const mapping = ref({});

function onFileChange(e) {
    // Produksi: parsing header excel di backend, di sini dummy
    headers.value = ["A", "B", "C"];
    mapping.value = Object.fromEntries(headers.value.map((h) => [h, ""]));
}

function submitImport() {
    const fd = new FormData();
    fd.append("file", document.querySelector("input[type=file]").files[0]);
    fd.append("mapping", JSON.stringify(mapping.value));
    fetch(`/${props.entity}/import`, { method: "POST", body: fd });
}
</script>
