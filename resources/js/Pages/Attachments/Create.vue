<template>
    <div>
        <h2>Upload Attachment</h2>
        <form @submit.prevent="submit" enctype="multipart/form-data">
            <div>
                <label>Task</label>
                <select v-model="form.task_id" required>
                    <option value="">-- Select Task --</option>
                    <option v-for="t in tasks" :value="t.id" :key="t.id">
                        {{ t.title }}
                    </option>
                </select>
            </div>
            <div>
                <label>PDF File (100-500KB)</label>
                <input
                    type="file"
                    accept="application/pdf"
                    @change="(e) => (form.file = e.target.files[0])"
                    required
                />
            </div>
            <div>
                <label>Is Public?</label>
                <input type="checkbox" v-model="form.is_public" />
            </div>
            <div>
                <label>Info (JSON)</label>
                <textarea v-model="form.info" />
            </div>
            <button type="submit">Upload</button>
            <Link href="/attachments">Back</Link>
            <span v-if="form.errors.file">{{ form.errors.file }}</span>
        </form>
    </div>
</template>
<script setup>
import { useForm, Link } from "@inertiajs/vue3";
const props = defineProps({ tasks: Array });
const form = useForm({ task_id: "", file: null, is_public: false, info: "{}" });
function submit() {
    form.post("/attachments", { forceFormData: true });
}
</script>
