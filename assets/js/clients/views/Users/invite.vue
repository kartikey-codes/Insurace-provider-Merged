<template>
	<div>
		<page-header :fluid="false">
			<template #title>
				<router-link :to="{ name: 'users' }" v-text="`Users /`" />
				<span>Invite</span>
			</template>
		</page-header>

		<b-container class="my-4">
			<b-row>
				<b-col cols="12">
					<b-card>
						<UserInviteForm autofocus @cancel="$router.push({ name: 'users' })" @saved="saved">
							<template #before>
								<b-row>
									<b-col cols="12" class="mb-5">
										<p>
											You can invite additional user accounts to your organization for
											collaboration on cases and audits.
										</p>
										<p>
											The user will receive an email with a link to register or join if they
											already have an existing account.
										</p>
										<p>User accounts can only be associated with a single organization.</p>
									</b-col>
								</b-row>
							</template>
						</UserInviteForm>
					</b-card>
				</b-col>
			</b-row>
		</b-container>
	</div>
</template>

<script type="text/javascript">
import UserInviteForm from "@/clients/components/Users/InviteForm.vue";

export default {
	name: "ViewAddUser",
	components: {
		UserInviteForm,
	},
	data() {
		return {};
	},
	methods: {
		saved(response) {
			// Nothing, notification triggered in component
		},
	},
};
</script>
