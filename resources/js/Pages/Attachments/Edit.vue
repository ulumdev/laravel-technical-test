<template>
    <div>
        <h2>Edit Attachment</h2>
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
                <label>(Optional) New PDF</label>
                <input
                    type="file"
                    accept="application/pdf"
                    @change="(e) => (form.file = e.target.files[0])"
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
            <div>
                <label>Audit Note</label>
                <textarea
                    v-model="form.audit_note"
                    placeholder="Change notes (optional)"
                ></textarea>
            </div>
            <button type="submit">Save</button>
            <Link href="/attachments">Back</Link>
            <span v-if="form.errors.file">{{ form.errors.file }}</span>
        </form>
    </div>
</template>
<script setup>
import { useForm, Link } from "@inertiajs/vue3";
const props = defineProps({ attachment: Object, tasks: Array });
const form = useForm({
    task_id: props.attachment.task_id,
    file: null,
    is_public: !!props.attachment.is_public,
    info: props.attachment.info,
    audit_note: props.attachment.auditCustomNote || "",
});
function submit() {
    form.post(`/attachments/${props.attachment.id}`, {
        forceFormData: true,
        _method: "put",
    });
}
</script>
