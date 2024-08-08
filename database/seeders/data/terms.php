<?php
$privacy = <<<EOB
# Privacy Policy

## Overview

At [Company Name], we prioritize the protection of your personal information. This policy outlines our practices regarding data collection, usage, and safeguarding.

## Data Collection and Usage

We gather your personal information to:

1. Deliver and enhance our services
2. Ensure security and compliance
3. Meet legal obligations
4. Conduct statistical analysis

We may also use your data for marketing purposes, including informing you about new products, events, or special offers. You can opt out of promotional communications at any time.

## Information Sharing

We maintain strict confidentiality of your data, sharing it only with employees who need it to provide services. We may disclose information if required by law or legal processes.

## Data Security

We employ robust physical, electronic, and managerial measures to protect your information. Our website uses SSL encryption for sensitive data transmission. We recommend you take personal precautions to protect your data online.

## Your Rights

You have the right to:

1. Access your personal information
2. Correct inaccuracies in your data
3. Request deletion of your data ("right to be forgotten")

To exercise these rights, contact us at [contact email]. We'll respond to your request within one month.

## Cookies

Our website uses cookies for analytics and to facilitate the login process. Cookies are only set with your consent and expire within four weeks.

## Policy Updates

We may update this policy periodically. Please review it regularly to stay informed about how we protect your personal information.

## Contact Us

If you have any questions about this privacy policy, please contact us at [contact email].
EOB;

$terms = <<<PVC
# Website Terms and Conditions

1. **Account Registration and Eligibility**
   - Users must be of legal age (typically 18+) to register an account.
   - Only one account per person is permitted.
   - Users must provide accurate, current, and complete information during registration.
   - The website reserves the right to verify user information and refuse or close accounts at its discretion.

2. **User Responsibilities and Account Security**
   - Users are responsible for maintaining the confidentiality of their login credentials.
   - All activities occurring under a user's account are the user's responsibility.
   - Users must immediately notify the website of any unauthorized use of their account.
   - The website is not liable for losses due to compromised user accounts.

3. **Deposits, Withdrawals, and Financial Transactions**
   - The website sets minimum and maximum limits for deposits and withdrawals.
   - Users must use their own funds and payment methods in their name.
   - The website may require identity verification before processing withdrawals.
   - Transaction fees may apply and will be clearly communicated to users.
   - The website reserves the right to conduct additional checks to prevent money laundering and fraud.

4. **Service Usage and Betting Rules**
   - Bets or transactions cannot be cancelled once confirmed by the user.
   - The website reserves the right to void bets or transactions in cases of technical errors, suspicious activity, or violation of terms.
   - Users are responsible for checking their bet details before confirmation.
   - The website's decision regarding any disputes is final.

5. **Promotions, Bonuses, and Loyalty Programs**
   - The website may offer various promotions, bonuses, and loyalty programs.
   - Each offer is subject to its own specific terms and conditions.
   - The website reserves the right to modify, suspend, or cancel any promotion at any time.
   - Abuse of promotions may result in account suspension and forfeiture of bonuses.

6. **Prohibited Activities and Fair Usage**
   - Users must not engage in any fraudulent or illegal activities.
   - The use of artificial intelligence, bots, or automated systems to gain unfair advantages is prohibited.
   - Users must not collude with others to manipulate services or outcomes.
   - Any attempt to exploit system vulnerabilities or errors is strictly forbidden.

7. **Account Suspension, Termination, and Closures**
   - The website reserves the right to suspend or terminate accounts for violations of terms, suspicious activity, or at its discretion.
   - Users may close their accounts voluntarily at any time.
   - The website may impose cooling-off periods or self-exclusion options for responsible gaming.
   - Account closure procedures, including the handling of remaining balances, will be clearly defined.

8. **Intellectual Property and Content Usage**
   - All content on the website is protected by copyright and other intellectual property laws.
   - Users may not reproduce, distribute, or create derivative works from the website's content without explicit permission.
   - User-generated content remains the property of the user, but the website is granted a license to use it.

9. **Privacy, Data Protection, and Cookie Usage**
   - The website collects and uses personal data in accordance with its privacy policy and applicable data protection laws.
   - Users consent to the collection and use of their data for service provision, improvement, and marketing purposes.
   - Users have the right to access, correct, and request deletion of their personal data.
   - The website uses cookies and similar technologies to enhance user experience and may allow third-party cookies.

10. **Governing Law, Jurisdiction, and Dispute Resolution**
    - The terms are governed by the laws of the website's jurisdiction of incorporation.
    - Any disputes will be resolved in the courts of that jurisdiction, unless otherwise specified.
    - The website may offer alternative dispute resolution methods before resorting to legal action.
    - Users agree to attempt to resolve any issues with the website directly before seeking external resolution.
PVC;

return [
    'terms' => $terms,
    'privacy' => $privacy,
];
