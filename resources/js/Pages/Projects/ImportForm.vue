<template>
    <form @submit.prevent="importExcel" enctype="multipart/form-data">
        <input
            type="file"
            accept=".xlsx,.xls,.csv"
            @change="fileChanged"
            required
        />
        <div v-for="f in dbFields" :key="f" style="margin-bottom: 0.25em">
            <label
                >{{ f }}
                <input v-model="mapping[f]" placeholder="Kolom di file..."
            /></label>
        </div>
        <button type="submit" :disabled="!file || loading">Import</button>
        <span v-if="loading">Uploading...</span>
    </form>
</template>

<script setup>
import { ref } from "vue";
const props = defineProps({ dbFields: Array, entity: String });
const emit = defineEmits(["imported"]);
const file = ref(null);
const mapping = ref({});
const loading = ref(false);

function fileChanged(e) {
    file.value = e.target.files[0];
}

async function importExcel() {
    if (!file.value) return;
    loading.value = true;
    const formData = new FormData();
    formData.append("file", file.value);
    formData.append("mapping", JSON.stringify(mapping.value));

    // Pastikan meta csrf-token ada di <head>
    const tokenMeta = document.querySelector('meta[name="csrf-token"]');
    if (!tokenMeta) {
        alert("CSRF token not found.");
        loading.value = false;
        return;
    }
    const token = tokenMeta.getAttribute("content");

    try {
        const res = await fetch(`/${props.entity}/import`, {
            method: "POST",
            body: formData,
            headers: {
                "X-CSRF-TOKEN": token,
                "Accept": "application/json",
            },
            credentials: "same-origin",
        });
        // Tangkap error validasi / server
        const contentType = res.headers.get("content-type") || "";
        if (!res.ok) {
            if (contentType.includes("application/json")) {
                const data = await res.json();
                throw new Error(data.message || JSON.stringify(data));
            } else {
                const text = await res.text();
                throw new Error(text);
            }
        }
        const data = await res.json();
        emit("imported", data.job_id);
    } catch (err) {
        alert("Gagal upload: " + err.message);
    } finally {
        loading.value = false;
    }
}
</script>
