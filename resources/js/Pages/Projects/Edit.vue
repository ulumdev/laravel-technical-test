<template>
    <div>
        <h2>Edit Project</h2>
        <form @submit.prevent="submit">
            <div>
                <label>Name</label>
                <input v-model="form.name" required />
            </div>
            <div>
                <label>Details (JSON)</label>
                <textarea v-model="form.details" />
            </div>
            <div>
                <label>Is Active</label>
                <input type="checkbox" v-model="form.is_active" />
            </div>
            <div>
                <label>Start Date</label>
                <input type="datetime-local" v-model="form.start_date" />
            </div>

            <div>
                <label>Audit Note</label>
                <textarea
                    v-model="form.audit_note"
                    placeholder="Change notes (optional)"
                ></textarea>
            </div>
            <button type="submit">Save</button>
            <Link href="/projects">Back</Link>
        </form>
        <span v-if="form.errors.name">{{ form.errors.name }}</span>
        <span v-if="form.errors.details">{{ form.errors.details }}</span>
    </div>
</template>
<script setup>
import { useForm, Link } from "@inertiajs/vue3";
const props = defineProps({ project: Object });
const form = useForm({
    name: props.project.name,
    details: props.project.details,
    is_active: !!props.project.is_active,
    start_date: props.project.start_date
        ? props.project.start_date.substring(0, 16)
        : "",
    audit_note: props.project.auditCustomNote || "",
});
function submit() {
    form.put(`/projects/${props.project.id}`);
}
</script>
