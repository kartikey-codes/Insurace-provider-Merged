<?php

use Cake\Core\Configure;

$appName = Configure::readOrFail('App.name');

$this->assign('title', __('Client Terms of Service & Agreement'));

// Version is stored in app configuration
$versionDate = Configure::read('TermsOfService.clientDate');
?>
<div class="container">
	<div class="bg-white card p-4 shadow rounded-lg mb-4">
		<div class="row mb-4">
			<div class="col-12">
				<div class="row">
					<div class="col-sm-12 mb-4 text-center">
						<h1 class="h2 font-weight-bold mb-2">
							<?= $appName ?> Terms of Service & Agreement
						</h1>
						<h6 class="text-muted">Last updated <?= date('n/d/Y', strtotime($versionDate)); ?></h6>
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						<p>
							This HIPAA Agreement (“Agreement”) is entered into this <?= date('j') ?> day of <?= date('F'); ?>, <?= date('Y') ?> by and between Health System
							Applications Inc., a Delaware Corporation (RevKeep™), and the organization entering into this agreement (“the Practice”).
						</p>
						<p>
							RevKeep™ and the Practice have entered into an agreement under which RevKeep™, is or will be rendering services on
							behalf of the Practice (the “Agreement”). RevKeep™ acknowledges that in the course of providing services under the
							Agreement, it will come into possession of Protected Health Information (“PHI”).
						</p>
						<p>
							The Administrative Simplification provisions of the Health Insurance Portability and Accountability Act of 1996, as
							amended, and its implementing regulations codified at 45 Code of Federal Regulations (“C.F.R.”), Parts 160-64
							(collectively, “HIPAA”), requires that certain privacy and security provisions be incorporated into the Agreement.
						</p>
						<p>
							Section 13404 of the Health Information Technology for Economic and Clinical Health (HITECH) Act (“HITECH Act”) (42
							U.S.C. §17934), Title XIII of Division A and Title IV of Division B of the American Recovery and Reinvestment Act of
							2009 requires that certain requirements of the HITECH Act be incorporated into the Agreement.
						</p>
						<p>
							This HIPAA Agreement amends the Agreement, and satisfies the obligations of RevKeep™ and THE Practice under HIPAA
							and the HITECH Act with respect to RevKeep™’s use and disclosure of Protected Health Information.
						</p>
						<p>
							RevKeep™ and THE Practice agree as follows:
						</p>
					</div>
				</div>
				<div class="row">
					<div class="col-12 text-center text-muted my-4">
						<h2>EXHIBIT B</h2>
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						<h3>Section 1: Applicable Law and Policy.</h3>

						<p>
							<strong>1.1</strong>
							RevKeep™ acknowledges that if it performs services or assists THE Practice in the performance of a function or
							service that involves the use or disclosure of PHI, then the Health Insurance Portability and Accountability Act of
							1996, as amended (“HIPAA”), and stricter state and federal laws, as applicable, require that the PHI be protected
							from
							inappropriate uses or disclosures.
						</p>

						<p>
							<strong>1.2</strong>
							RevKeep™ acknowledges that under §13404 of the Health Information Technology for Economic and Clinical Health
							Act
							(“HITECH Act”) (42 U.S.C. §17934), Title XIII of Division A and Title IV of Division B of the American Recovery and
							Reinvestment Act of 2009 (ARRA) (Pub. L. 111-5)), its use and disclosure of PHI must be in compliance with the terms
							of
							this Agreement and 45 C.F.R. §164.504(e). RevKeep™ agrees that the additional requirements of the HITECH Act
							relating to
							privacy that are applicable with respect to covered entities are applicable to RevKeep™™ and are incorporated into
							this
							Agreement.
						</p>

						<p>
							<strong>1.3</strong>
							Capitalized terms not otherwise defined shall have the meaning as set forth in HIPAA or the HITECH Act.
						</p>
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						<h3>Section 2: Use and Disclosure of PHI.</h3>
						<p>
							<strong>2.1</strong>
							PHI, in electronic form or otherwise, may be used or disclosed only when required by law or as necessary to
							enable
							RevKeep™ to satisfy the obligations and to perform the functions, activities, services and operations to which
							RevKeep™
							is contractually obligated to provide to THE Practice. RevKeep™ shall not and shall ensure that its directors,
							officers,
							employees, contractors and agents, do not, use PHI received from THE Practice in any manner that would constitute a
							violation of applicable law.
						</p>
						<p>
							<strong>2.2</strong>
							RevKeep™ shall not and shall ensure that its directors, officers, employees, contractors, and agents do not
							disclose
							PHI received from THE Practice in any manner that would constitute a violation of applicable law if disclosed by THE
							Practice. RevKeep™ may disclose PHI (a) as permitted and pursuant to the requirements of this Agreement or (b) as
							required by law.
						</p>
						<p>
							<strong>2.3</strong>
							To the extent RevKeep™ discloses PHI to a third party, must obtain, prior to making any such disclosure:
						</p>
						<p>
							<strong>2.3.1</strong>
							Reasonable assurances evidenced by written contract from such third party that PHI will be held confidential
							and
							safeguarded consistent with the terms of this Agreement, and only used or further disclosed for the purpose for
							which
							RevKeep™ disclosed it to the third party or as required by law; and
						</p>
						<p>
							<strong>2.3.2</strong>
							An agreement from such third party to immediately notify RevKeep™ (who will in turn notify THE Practice in
							accordance with Section 4 of this Agreement) of any:
						</p>
						<p>
							<strong>2.3.2.1</strong>
							Unauthorized access, use or disclosure of PHI;
						</p>
						<p>
							<strong>2.3.2.2</strong>
							Security Incident as defined in 45 C.F.R. §164.304 and further explained in Section 4.2 of this Agreement;
							and
						</p>
						<p>
							<strong>2.3.2.3</strong>
							Breaches of the confidentiality of the PHI, as Breach is defined by §13400(1)(A) (42 U.S.C. §17921(1)(A)) of
							the
							HITECH Act,
							to the extent such third party has discovered such unauthorized access, use or disclosure of PHI, Security Incident
							or
							Breach.
						</p>
						<p>
							<strong>2.4</strong>
							RevKeep™ shall utilize a Limited Data Set, if practicable, for all uses, disclosures or requests of PHI.
							Otherwise,
							any uses or disclosures of PHI shall be limited to the “Minimum Necessary,” as defined in 45 C.F.R. §514(d) and
							pursuant
							to the requirements set forth in the HITECH Act at §13405(b). RevKeep™ acknowledges its obligation under
							§13405(b)(2)
							(42 U.S.C. §17935(b)(2)) of the HITECH Act to determine what constitutes the minimum necessary to accomplish the
							intended purposes of any disclosure of PHI.
						</p>
						<p>
							<strong>2.5</strong>
							To the extent RevKeep™ more of THE Practice’s obligation(s) under Subpart E of 45 CFR Part 164 (“Privacy Rule”),
							RevKeep™ shall comply with all requirements of the Privacy Rule that apply to THE Practice in the performance of
							such
							obligation(s).
						</p>
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						<h3>Section 3: Safeguards against Misuse of Information.</h3>
						<p>
							<strong>3.1</strong>
							RevKeep™ agrees that it will implement all appropriate safeguards to prevent the access, use, or disclosure of
							PHI
							other than pursuant to the terms and conditions of this Agreement. Such safeguards include administrative, physical,
							and
							technical safeguards that reasonably and appropriately protect the Confidentiality, Integrity, and Availability of
							the
							electronic PHI that it creates, receives, maintains, or transmits on behalf of THE Practice as required by 45 CFR
							Part
							160 and Subparts A and C of Part 164 (“Security Rule”). In addition, RevKeep™ agrees to comply in all respects with
							the
							Security Rule, and that it shall implement the Security Rule requirements set forth in 45 C.F.R. §§164.308, 164.310,
							164.312, and 164.316 and acknowledges that these requirements shall apply to RevKeep™ in the same manner as they
							apply
							to THE Practice.
						</p>
						<p>
							<strong>3.2</strong>
							RevKeep™ will require any of its subcontractors and agents, to which RevKeep™ is permitted by this Agreement or
							in
							writing by THE Practice to disclose PHI, to enter into a written business associate agreement, in form substantially
							similar to this Agreement, which requires such subcontractor or agent to comply with the same privacy and security
							safeguard obligations with respect to PHI that are applicable to RevKeep™ under this Agreement, including but not
							limited to the provisions set forth in Section 2.3.
						</p>
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						<h3>Section 4: Reporting of Disclosures of PHI, Breaches & Security Incidents.</h3>
						<p>
							<strong>4.1</strong>
							RevKeep™ shall, without unreasonable delay and within ten (10) calendar days of RevKeep™’s discovery of: (a) a
							Security Incident (as defined in 45 C.F.R. §164.304 and further explained below), (b) the Breach of unsecured PHI
							(as
							defined in §13402(h) of the HITECH Act), or (c) an access, use or disclosure of PHI in violation of this Agreement
							by
							RevKeep™, its officers, directors, employees, contractors, or agents, or by a third party to which RevKeep™
							disclosed
							PHI pursuant to Section 2 of this Agreement, report any such disclosure to THE Practice. For this purpose, discovery
							means the first day on which the applicable incident is known to RevKeep™ or by reasonable diligence would have been
							known to RevKeep™. RevKeep™ shall only be deemed to have knowledge of such an incident if the incident is known or
							by
							exercising reasonable diligence would have been known to any person, other than the person committing the breach,
							who is
							an employee, officer, subcontractor or other agent of RevKeep™.
						</p>
						<p>
							<strong>4.2</strong>
							The HIPAA Security Rule defines a “Security Incident” as an attempted or successful unauthorized access, use,
							disclosure, modification or destruction of information or interference with system operations in an information
							system,
							involving PHI that is created, received, maintained or transmitted by or on behalf of THE Practice in electronic
							form
							(45 C.F.R. §164.304). RevKeep™ shall also notify THE Practice of attempts to bypass RevKeep™’s electronic security
							mechanisms.
						</p>
						<p>
							<strong>4.2.1</strong>
							Both parties recognize, however, that the significant number of meaningless attempts to, without
							authorization,
							access, use, disclose, modify or destroy PHI in RevKeep™’s information systems could make a real-time reporting
							requirement formidable for both parties. Both parties believe that the Security Rule notice requirements are met by
							instituting a process by which:
						</p>
						<p>
							<strong>4.2.1.1</strong>
							RevKeep™ discloses to THE Practice the rate and types of attempted incidents that are occurring at the time
							this
							Agreement is signed;
						</p>
						<p>
							<strong>4.2.1.2</strong>
							RevKeep™ monitors the rate and nature of such attempts over time; and
						</p>
						<p>
							<strong>4.2.1.3</strong>
							RevKeep™ reports to THE Practice any substantive changes to the rate or nature of such attempts that could
							adversely affect THE Practice directly or indirectly.
						</p>
						<p>
							<strong>4.2.2</strong>
							The following are illustrative of unsuccessful security incidents when they do not result in unauthorized
							access,
							use, disclosure, modification, or destruction of PHI or interference with an information system:
						</p>
						<p>
							<strong>4.2.2.1</strong> Pings on a firewall;
						</p>
						<p>
							<strong>4.2.2.2</strong> Port scans;
						</p>
						<p>
							<strong>4.2.2.3</strong> Attempts to log on to a system or enter a database with an invalid password or username;
							and
						</p>
						<p>
							<strong>4.2.2.4</strong> Malware (e.g., worms, viruses).
						</p>
						<p>
							<strong>4.2.3</strong>
							If RevKeep™ observes through ongoing monitoring successful Security Incidents that extend beyond these
							routine,
							unsuccessful attempts in such a way that they could impact the Confidentiality, Integrity or Availability of PHI,
							RevKeep™ agrees to promptly notify THE Practice.
						</p>
						<p>
							<strong>4.3</strong>
							If RevKeep™ is required to report (a) a Security Incident, (b) a data Breach, or (c) any other non-permitted
							access,
							use or disclosure of PHI, such report shall be in writing and shall include at a minimum:
						</p>
						<p>
							<strong>4.3.1</strong>
							The date and time the event occurred and the date it was discovered;
						</p>
						<p>
							<strong>4.3.2</strong>
							A complete description of the PHI accessed, used, or disclosed;
						</p>
						<p>
							<strong>4.3.3</strong>
							A complete description of the event and its cause;
						</p>
						<p>
							<strong>4.3.4</strong>
							Contact information for communications regarding the event;
						</p>
						<p>
							<strong>4.3.5</strong>
							A description of the initial mitigation steps taken to contain the event;
						</p>
						<p>
							<strong>4.3.6</strong>
							A description of the plan to prevent reoccurrences of the event in the future.
						</p>
						<p>
							<strong>4.4</strong>
							RevKeep™ shall send all reports required by this Section to the addressee indicated below unless The Practice
							provides RevKeep™ with a written notification to send such reports elsewhere.
						</p>

						<p>
							Name:______________________________________________________
						</p>
						<p>
							Address: ____________________________________________________<br />
							___________________________________________________________
						</p>
						<p>
							Telephone: __________________________________________________
						</p>
						<p>
							Fax:________________________________________________________
						</p>

						<p>
							<strong>4.5</strong>
							RevKeep™ shall comply with applicable laws that require notification to individuals in the event of an
							unauthorized
							access to or release of personally-identifiable information (“PII”) or PHI, as defined by applicable state or
							federal
							law, or other event requiring notification (“Notification Event”), whether such Notification Event was the
							responsibility of RevKeep™ or a third party to which RevKeep™ disclosed PII or PHI. When notification to individuals
							is
							required by law or determined by THE Practice, in its sole discretion, to be necessary under this Agreement, whether
							such Notification Event was the responsibility of RevKeep™ or a third party to which RevKeep™ disclosed PII or PHI,
							RevKeep™ shall coordinate with THE Practice to (a) investigate the Notification Event, (b) inform all affected
							individuals and (c) mitigate the Notification Event. At THE Practice’s sole discretion, mitigation includes but is
							not
							limited to securing credit monitoring or protection services for affected individuals.
						</p>

						<p>
							<strong>4.6</strong>
							RevKeep™ agrees to indemnify and hold THE Practice harmless from any and all liability, damages, costs
							(including
							reasonable attorney fees and costs) and expenses imposed upon or asserted against THE Practice arising out of any
							claims, demands, awards, settlements, fines or judgments relating to RevKeep™’s access, use, or disclosure of PHI
							contrary to the provisions of this Agreement.
						</p>
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						<h3>Section 5: Agreements by Third Parties.</h3>
						<p>
							RevKeep™ shall enter into an agreement with any agent or subcontractor that will
							have access to PHI that is received from, or created or received by RevKeep™ on behalf of, THE Practice pursuant to
							which such agent or subcontractor agrees to be bound by the same restrictions, terms, and conditions that apply to
							RevKeep™ pursuant to this Agreement with respect to such PHI, including that such agent or subcontractor implement
							reasonable and appropriate safeguards to protect it as described in Section 3 above.
						</p>
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						<h3>Section 6: Access to Information.</h3>
						<p>
							<strong>6.1</strong>
							Within five (5) business days of a request by THE Practice for access to PHI about an individual, RevKeep™ shall
							make available to THE Practice such PHI for so long as such information is maintained by RevKeep™.
						</p>
						<p>
							<strong>6.2</strong>
							In the event any individual requests access to PHI directly from RevKeep™, RevKeep™ shall within two (2)
							business
							days forward such request to THE Practice. Any denials of access to the PHI requested shall be the responsibility of
							THE
							Practice.
						</p>
						<p>
							<strong>6.3</strong>
							To the extent RevKeep™ maintains an Electronic Health Record, as that term is defined in §13400(5) (42 U.S.C.
							§17921(5)) of the HITECH Act, with respect to PHI of an individual, RevKeep™ agrees that the individual, and THE
							Practice on behalf of the individual, shall have a right to obtain a copy of such information in electronic format.
							RevKeep™ also agrees to transmit an electronic copy of Electronic Health Record information directly to a person or
							entity designated by the individual, or designated by THE Practice on behalf of the individual, provided the
							direction
							is clear, conspicuous, and specific.
						</p>
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						<h3>Section 7: Availability of PHI for Amenthent.</h3>
						<p>
							Within ten (10) business days of receipt of a request from THE Practice
							for the amendment of an individual's PHI, RevKeep™ shall provide such information to THE Practice for amendment and
							incorporate any such amendments in the PHI as required by 45 C. F .R. § 164.526.
						</p>
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						<h3>Section 8: Accounting of Disclosures.</h3>
						<p>
							<strong>8.1</strong>
							Within ten (10) business days of notice by THE Practice to RevKeep™ that it has received a request for an
							accounting
							of disclosures of PHI regarding an individual during the six (6) years prior to the date on which the accounting was
							requested, RevKeep™ shall make available to THE Practice such information as is in RevKeep™'s possession and is
							required
							for THE Practice to make the accounting required by 45 C.F.R. §164.528.
						</p>
						<p>
							<strong>8.2</strong>
							To the extent RevKeep™ maintains PHI as an Electronic Health Record, RevKeep™ acknowledges that the exception at
							45
							C.F.R. §164.528(a)(1)(i) not requiring disclosures for the purpose of carrying out Treatment, Payment, and
							Accounting
							Operations is inapplicable and that these disclosures must be tracked for three years.
						</p>
						<p>
							<strong>8.3</strong>
							For disclosures that it is required to track, at a minimum, RevKeep™ shall provide THE Practice with the
							following
							information:
						</p>
						<p>
							<strong>8.3.1</strong> the date of the disclosure;
						</p>
						<p>
							<strong>8.3.2</strong> the name of the entity or person who received the PHI, and if known, the address of such
							entity or person;
						</p>
						<p>
							<strong>8.3.3</strong> a brief description of the PHI disclosed;
						</p>
						<p>
							<strong>8.3.4</strong> a brief statement of the purpose of such disclosure which includes an explanation of the
							basis for such
							disclosure; and
						</p>
						<p>
							<strong>8.3.5</strong>
							RevKeep™ further shall provide any additional information to the extent required by the
							HITECH Act and any
							accompanying regulations.
						</p>
						<p>
							<strong>8.4</strong>
							In the event the request for an accounting is delivered directly to RevKeep™, RevKeep™ shall
							within two (2)
							business
							days forward such request to THE Practice. It shall be THE Practice's responsibility to prepare and deliver any such
							accounting requested.
						</p>
						<p>
							<strong>8.5</strong>
							RevKeep™ hereby agrees to implement an appropriate recordkeeping process to enable it to comply
							with the
							requirements of this Section.
						</p>
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						<h3>Section 9: Restriction Agreements and Confidential Communications.</h3>
						<p>
							RevKeep™ shall comply with any agreement that THE
							Practice makes that either (a) restricts use or disclosure of PHI pursuant to 45 C.F.R. §164.522(a) or (b) requires
							Confidential Communication about PHI pursuant to 45 C.F.R. §164.522(b), provided THE Practice notifies RevKeep™ of
							the
							restriction or Confidential Communication obligations. THE Practice shall promptly notify RevKeep™ in writing of the
							termination of any such restriction agreement or Confidential Communication requirement, and with respect to
							termination
							of such restriction agreement, instruct RevKeep™ whether any PHI will remain subject to the terms of the restriction
							agreement.
						</p>
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						<h3>Section 10: Restriction on Remuneration for EHR, PHI, and Marketing.</h3>
						<p>
							RevKeep™ shall not directly or indirectly receive
							remuneration in exchange for any PHI except as permitted by §13405(d)) (42 U.S.C. §17935(d)) of the HITECH Act. In
							addition, RevKeep™ shall not directly or indirectly receive remuneration in connection with a communication to
							purchase
							or use a product except as permitted by §13406(a) (42 U.S.C. §17936(a)) of the HITECH Act.
						</p>
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						<h3>Section 11: Availability of Books and Records.</h3>
						<p>
							RevKeep™ hereby agrees to make its internal practices, books, and records
							relating to the use and disclosure of PHI received from, or created or received by RevKeep™ on behalf of, THE
							Practice
							available to the Secretary of the Department of Health and Human Services for purposes of determining THE Practice's
							and
							RevKeep™’s compliance with the Standards for Privacy and Security of Individually Identifiable Health Information,
							45
							C.F.R. Parts 160 and 164 (“Privacy and Security Standards”).
						</p>
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						<h3>Section 12: Termination and Return of Records.</h3>
						<p>
							<strong>12.1</strong>
							Upon termination of the Agreement, RevKeep™ shall, if feasible, return or destroy all PHI received from, or
							created
							or received by the RevKeep™ on behalf of, THE Practice that RevKeep™ still maintains in any form and retain no
							copies of
							such information.
						</p>
						<p>
							<strong>12.1.1</strong>
							RevKeep™ will require any subcontractor or agent, to which RevKeep™ has disclosed PHI, to, if feasible,
							return
							such PHI to RevKeep™ (so that RevKeep™ may return it to THE Practice) or destroy all PHI in whatever form or medium
							received from RevKeep™, including all copies thereof and all data, compilations, and other works derived therefrom
							that
							allow identification of any individual who is a subject of the PHI, and certify to RevKeep™ that all such
							information
							has been returned or destroyed.
						</p>
						<p>
							<strong>12.1.2</strong> RevKeep™ will complete these obligations as promptly as possible, but not later than
							forty-five (45) business
							days following the effective date of the termination or other conclusion of the Agreement.
						</p>
						<p>
							<strong>12.2</strong> If such return or destruction of PHI by RevKeep™ or their subcontractor or agent is not
							feasible, RevKeep™ and
							their subcontractors and agents shall limit their further use or disclosure of such information to the purposes that
							make return or destruction of the PHI infeasible.
						</p>
						<p>
							<strong>12.3</strong> RevKeep™’s obligation to protect the privacy and safeguard the security of PHI as specified in
							this Agreement
							will
							be continuous and survive termination or other conclusion of the Agreement or any other agreements, including
							statements
							of work, entered into between RevKeep™ and THE Practice.
						</p>
						<p>
							<strong>12.4</strong>
							If either party has violated the provisions of this Agreement, either party may immediately terminate the
							Agreement
							and any other agreements, including statements of work, entered into between the parties that require RevKeep™ to
							access, use or disclose PHI.
						</p>
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						<h3>Section 13: Compliance with Transaction Standards.</h3>
						<p>
							<strong>13.1</strong>
							If RevKeep™ conducts in whole or part electronic Transactions on behalf of THE Practice for which Department of
							Health and Human Services (DHHS) has established Standards, RevKeep™ will comply, and will require any subcontractor
							or
							agent it involves with the conduct of such Transactions to comply, with each applicable requirement of the
							Transaction
							Rule, 45 C.F.R. Part 162.
						</p>
						<p>
							<strong>13.2</strong> RevKeep™ will not enter into, or permit its subcontractors or agents to enter into, any
							Trading Partner
							Agreement
							in connection with the conduct of Standard Transactions on behalf of THE Practice that:
						</p>
						<p>
							<strong>13.2.1</strong> Changes the definition, data condition, or use of a data element or segment in a Standard
							Transaction;
						</p>
						<p>
							<strong>13.2.2</strong> Adds any data element or segment to the maximum defined data set;
						</p>
						<p>
							<strong>13.2.3</strong> Uses any code or data element that is marked “not used” in the Standard Transaction’s
							implementation
							specification or is not in the Standard Transaction’s implementation specification; or
						</p>
						<p>
							<strong>13.2.4</strong> Changes the meaning or intent of the Standard Transaction’s implementation specification.
						</p>
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						<h3>Section 14: Amendment.</h3>
						<p>
							No provision of the Agreement or this Agreement may be modified except by a written document
							signed by a duly authorized representative of the parties except upon the effective date of any amendment to the
							Privacy
							Standards or the Security Rule or the effective date of any other final regulations with respect to PHI. In such a
							case,
							this Agreement will automatically be amended so that the obligations it imposes on RevKeep™ shall remain in
							compliance
							with such regulations.
						</p>
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						<h3>Section 15: Assignment.</h3>
						<p>
							No party may assign or transfer any or all of its rights and/or obligations under this Agreement
							or any part of it, nor any benefit or interest in or under it, to any third party without the prior written consent
							of
							the other party, which shall not be reasonably withheld.
						</p>
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						<h3>Section 16: Conflicts.</h3>
						<p>
							The terms and conditions of this Agreement supersede and override any other Health Insurance
							Portability and Accountability Act of 1996 (HIPAA) terms and conditions contained within any agreements, including
							statements of work, entered into by THE Practice and RevKeep™, including but not limited to, any agreements with its
							subsidiaries, affiliates, parent companies, officers, directors, employees, contractors, and/or agents.
						</p>
					</div>
				</div>
				<hr />
				<div class="row">
					<div class="col-12 mb-4">
						<h2>Signatures</h2>
						<p>
							This Agreement is agreed to by both parties as witnessed by their respective signatures below. By signing this
							Agreement, the signatory certifies and warrants that he or she has the actual authority to bind RevKeep™ to this
							Agreement for all of RevKeep™’s agreements and statements of work with THE Practice. Notwithstanding any statement
							to
							the contrary in any other agreements and statements of work between RevKeep™ and The Practice, this Agreement is
							effective when signed by the appropriate THE Practice Representative and RevKeep™.
						</p>
					</div>
				</div>
				<div class="row">
					<div class="col-6">
						<h4 class="text-muted mb-4">THE PRACTICE</h4>
						<p>Name:_____________________________</p>
						<p>Title:______________________________</p>
						<p>Date: <?= date('n/d/Y') ?></p>
					</div>
					<div class="col-6">
						<h4 class="text-muted mb-4">REVKEEP™</h4>
						<p>Name: Kevin Lasser</p>
						<p>Title: Owner</p>
						<p>Date: <?= date('n/d/Y') ?></p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
