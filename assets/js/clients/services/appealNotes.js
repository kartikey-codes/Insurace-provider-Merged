import api from "@/api";
const url = "/appeals";
const subpart = "notes";

export async function addNote(id, params) {
	const response = await api.post(`${url}/${id}/${subpart}`, {
		notes: params.notes,
	});
	return response.data.data;
}

export async function deleteNote(appeal_id, id) {
	const response = await api.delete(`${url}/${appeal_id}/${subpart}/${id}`);
	return response.data; // { success: bool, data: {} }
}
