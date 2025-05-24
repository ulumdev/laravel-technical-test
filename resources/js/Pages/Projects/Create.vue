<template>
    <div>
        <h2>Create Project</h2>
        <form @submit.prevent="submit">
            <div>
                <label>Name</label>
                <input type="text" v-model="form.name" required />
            </div>

            <div>
                <label>Details (JSON)</label>
                <textarea v-model="form.details"></textarea>
            </div>

            <div>
                <label>Is Active</label>
                <input type="checkbox" v-model="form.is_active" />
            </div>
            <div>
                <label>Start Date</label>
                <input type="datetime-local" v-model="form.start_date" />
            </div>
            <button type="submit">Create</button>
            <Link href="/projects">Back</Link>
        </form>
        <span v-if="form.errors.name">{{ form.errors.name }}</span>
        <span v-if="form.errors.details">{{ form.errors.details }}</span>
    </div>
</template>

<script setup>
import { useForm, Link } from "@inertiajs/vue3";
const form = useForm({
    name: "",
    details: "",
    is_active: true,
    start_date: "",
});
function submit() {
    form.post("/projects");
}
</script>
