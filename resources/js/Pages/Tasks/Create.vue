<template>
    <div>
        <h2>Create Task</h2>
        <form @submit.prevent="submit">
            <div>
                <label>Title</label>
                <input v-model="form.title" required />
            </div>
            <div>
                <label>Project</label>
                <select v-model="form.project_id" required>
                    <option value="">-- Select Project --</option>
                    <option v-for="p in projects" :value="p.id" :key="p.id">
                        {{ p.name }}
                    </option>
                </select>
            </div>
            <div>
                <label>Due Date</label>
                <input type="datetime-local" v-model="form.due_date" />
            </div>
            <div>
                <label>Is Done?</label>
                <input type="checkbox" v-model="form.is_done" />
            </div>
            <div>
                <label>Metadata (JSON)</label>
                <textarea v-model="form.metadata" />
            </div>
            <button type="submit">Create</button>
            <Link href="/tasks">Back</Link>
        </form>
        <span v-if="form.errors.title">{{ form.errors.title }}</span>
    </div>
</template>
<script setup>
import { useForm, Link } from "@inertiajs/vue3";
const props = defineProps({ projects: Array });
const form = useForm({
    title: "",
    project_id: "",
    due_date: "",
    is_done: false,
    metadata: "{}",
});
function submit() {
    form.post("/tasks");
}
</script>
