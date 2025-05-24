<template>
    <form @submit.prevent="submit">
        <label>Title</label>
        <input v-model="form.title" type="text" required />
        <label>Project</label>
        <select v-model="form.project_id" required>
            <option
                v-for="project in projects"
                :key="project.id"
                :value="project.id"
            >
                {{ project.name }}
            </option>
        </select>
        <label>Due Date</label>
        <input v-model="form.due_date" type="datetime-local" required />
        <label>Is Done?</label>
        <input type="checkbox" v-model="form.is_done" />
        <label>Metadata (JSON)</label>
        <textarea v-model="form.metadata"></textarea>
        <button type="submit">Create</button>
    </form>
</template>

<script setup>
import { useForm } from "@inertiajs/vue3";
const props = defineProps({
    projects: Array,
});
const form = useForm({
    title: "",
    project_id: "",
    due_date: "",
    is_done: false,
    metadata: "{}",
});
function submit() {
    form.post("/tasks", {
        onSuccess: () => {
            form.reset();
        },
        onError: (errors) => {
            console.error(errors);
        },
    });
}
</script>
