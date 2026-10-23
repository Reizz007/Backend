<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiChatController extends Controller
{
    /**
     * Process an incoming customer message and return an AI assistant response.
     */
    public function chat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'history' => 'nullable|array',
        ]);

        $userMessage = trim($validated['message']);
        $lower = strtolower($userMessage);

        // Fetch live context from settings and catalog
        $promoCodes = Setting::get('promo_codes', [
            ['code' => 'NEURON20', 'percent' => 20, 'active' => true],
            ['code' => 'AIWELCOME', 'amount' => 10, 'active' => true],
        ]);
        $activeCodes = array_filter($promoCodes, fn ($p) => ! empty($p['active']));
        $promoCodeNames = array_column($activeCodes, 'code');

        $telegramUsername = Setting::get('telegram_username', 'chett4244');

        // Check intents
        $response = $this->determineResponse($lower, $userMessage, $promoCodeNames, $telegramUsername);

        return response()->json([
            'success' => true,
            'reply' => $response['reply'],
            'quick_replies' => $response['quick_replies'] ?? [],
            'actions' => $response['actions'] ?? [],
        ]);
    }

    /**
     * Determine contextual AI response based on customer query.
     */
    protected function determineResponse(string $lower, string $original, array $promoCodes, string $telegramUsername): array
    {
        // 1. Payment Process & How to Pay
        if (
            str_contains($lower, 'how to pay') ||
            str_contains($lower, 'process to pay') ||
            str_contains($lower, 'payment process') ||
            str_contains($lower, 'how do i pay') ||
            str_contains($lower, 'how can i pay') ||
            str_contains($lower, 'khqr') ||
            str_contains($lower, 'aba') ||
            str_contains($lower, 'bakong') ||
            str_contains($lower, 'bank') ||
            str_contains($lower, 'payway') ||
            str_contains($lower, 'scan qr') ||
            str_contains($lower, 'step to pay') ||
            str_contains($lower, 'how payment works')
        ) {
            $reply = "### 💳 How to Pay on Dyzzstore (Fast & Automated)\n\n"
                ."We accept **Cambodia National KHQR** for real-time, instant-verification payments!\n\n"
                ."**Step-by-step payment guide:**\n"
                ."1. **Select Your Tools or Subscription**: Add any AI model license or Pro AI Suite plan to your cart and click **Checkout**.\n"
                ."2. **Enter Customer Details**: Fill in your Name and Email address (this is where your instant license key & invoice will be sent).\n"
                ."3. **Scan KHQR Code**: A dynamic KHQR code with your exact order total will appear on screen.\n"
                ."4. **Pay with Mobile Banking**: Open **ABA Mobile**, **Bakong**, **ACLEDA**, **Canadia**, **Wing**, **Sathapana**, or any Cambodian banking app, select **Scan QR**, and confirm payment.\n"
                ."5. **Instant Automatic Activation**: Within 2–3 seconds of your scan, our gateway automatically confirms the transaction without refreshing! Your license keys and API credentials appear on screen and are sent to your email immediately.\n\n"
                .'💡 *Zero waiting for manual approval — delivery is 100% instant 24/7!*';

            return [
                'reply' => $reply,
                'quick_replies' => [
                    'Open Checkout Now',
                    'What banks are supported?',
                    'Where do I get my license key?',
                    'Contact human support',
                ],
                'actions' => [
                    ['type' => 'open_checkout', 'label' => '💳 Open Checkout'],
                    ['type' => 'browse_tools', 'label' => '🔍 View AI Tools'],
                ],
            ];
        }

        // 2. Supported Banks
        if (str_contains($lower, 'which bank') || str_contains($lower, 'supported bank') || str_contains($lower, 'what bank') || str_contains($lower, 'acleda') || str_contains($lower, 'wing')) {
            $reply = "### 🏦 Supported Banking & Wallet Apps\n\n"
                ."You can pay using **any Cambodian mobile banking app** that supports National KHQR, including:\n"
                ."- **ABA Mobile** (Instant USD & KHR)\n"
                ."- **Bakong Wallet**\n"
                ."- **ACLEDA Mobile**\n"
                ."- **Canadia Bank**\n"
                ."- **Wing Bank**\n"
                ."- **Sathapana Mobile**\n"
                ."- **Chip Mong Bank**\n"
                ."- **TrueMoney Cambodia**\n\n"
                .'Transactions are securely encrypted and automatically verified in real time!';

            return [
                'reply' => $reply,
                'quick_replies' => ['How do I pay with KHQR?', 'Open Checkout Now', 'Ask another question'],
                'actions' => [
                    ['type' => 'open_checkout', 'label' => '💳 Proceed to Checkout'],
                ],
            ];
        }

        // 3. License Key Delivery & Activation
        if (
            str_contains($lower, 'license') ||
            str_contains($lower, 'api key') ||
            str_contains($lower, 'activation') ||
            str_contains($lower, 'delivery') ||
            str_contains($lower, 'where is my key') ||
            str_contains($lower, 'how to get key') ||
            str_contains($lower, 'receive key')
        ) {
            $reply = "### ⚡ Instant License Key & API Delivery\n\n"
                ."Once your KHQR payment completes:\n"
                ."- **On-Screen Display**: Your cryptographically generated API key and license credentials will appear on the confirmation screen with a **1-click copy** button.\n"
                ."- **Email Receipt**: A backup copy with your invoice and activation guide is delivered to your email instantly.\n\n"
                .'All license credentials and activation links are delivered directly on screen and to your email upon payment.';

            return [
                'reply' => $reply,
                'quick_replies' => ['How do I pay with KHQR?', 'Contact human support', 'Refund policy'],
                'actions' => [],
            ];
        }

        // 4. Discounts & Promo Inquiries
        if (
            str_contains($lower, 'discount') ||
            str_contains($lower, 'coupon') ||
            str_contains($lower, 'promo') ||
            str_contains($lower, 'code') ||
            str_contains($lower, 'voucher') ||
            str_contains($lower, 'offer') ||
            str_contains($lower, 'deal')
        ) {
            $reply = "### 🏷️ Direct Lifetime Pricing\n\n"
                ."All AI tools and subscriptions on Dyzzstore are already offered at direct wholesale rates with **no extra promotion codes required**:\n\n"
                ."- **Perpetual Licenses**: From $29 one-time (pay once, keep forever)\n"
                ."- **Pro AI Suite**: All 45+ premier AI models unlocked\n"
                ."- **100% Commercial Rights**: Free updates and dedicated API credits\n\n"
                .'Simply select your tools and checkout with instant KHQR!';

            return [
                'reply' => $reply,
                'quick_replies' => ['How do I pay with KHQR?', 'Browse AI Tools', 'Contact human support'],
                'actions' => [
                    ['type' => 'open_checkout', 'label' => '💳 Proceed to Checkout'],
                    ['type' => 'browse_tools', 'label' => '🛍️ Browse AI Tools'],
                ],
            ];
        }

        // 5. Subscription vs Single Tool
        if (
            str_contains($lower, 'subscription') ||
            str_contains($lower, 'plan') ||
            str_contains($lower, 'difference') ||
            str_contains($lower, 'single tool') ||
            str_contains($lower, 'pro suite') ||
            str_contains($lower, 'pricing') ||
            str_contains($lower, 'cost')
        ) {
            $reply = "### 💎 Subscription vs Single Lifetime License\n\n"
                ."**1. Single Tool Lifetime License ($29 – $49 one-time)**\n"
                ."- Perpetual access to that specific AI model/software\n"
                ."- Free lifetime minor updates and dedicated API credits\n"
                ."- Pay once, keep forever (no recurring fees)\n\n"
                ."**2. Pro AI Suite Subscription ($49/month billed annually or $59/mo)**\n"
                ."- Instant access to **ALL 45+ premier AI models**\n"
                ."- Priority H100 SXM5 GPU cluster speeds\n"
                ."- Unlimited neural tokens & 10 high-speed API keys\n"
                .'- Full commercial rights with zero data retention';

            return [
                'reply' => $reply,
                'quick_replies' => ['How do I pay with KHQR?', 'What tools are available?', 'Are there discount codes?'],
                'actions' => [
                    ['type' => 'browse_tools', 'label' => '🔍 View AI Tools Catalog'],
                ],
            ];
        }

        // 6. Tools Catalog & Recommendations
        if (
            str_contains($lower, 'tool') ||
            str_contains($lower, 'model') ||
            str_contains($lower, 'claude') ||
            str_contains($lower, 'gpt') ||
            str_contains($lower, 'deepcode') ||
            str_contains($lower, 'midjourney') ||
            str_contains($lower, 'cursor') ||
            str_contains($lower, 'catalog')
        ) {
            $sampleTools = Product::take(5)->get(['name', 'price_monthly', 'price_lifetime', 'badge', 'short_desc']);
            $toolsText = '';
            if ($sampleTools->isNotEmpty()) {
                foreach ($sampleTools as $tool) {
                    $price = $tool->price_lifetime ? '$'.$tool->price_lifetime.' lifetime' : ($tool->price_monthly ? '$'.$tool->price_monthly.'/mo' : 'Featured');
                    $toolsText .= "- **{$tool->name}** ({$price}): {$tool->short_desc}\n";
                }
            } else {
                $toolsText = "- **Synapse Omni-LLM 4.5**: Frontier reasoning & coding engine.\n- **Visionary Diffusion X**: Ultra-photorealistic 8K generation.\n- **DeepCode Copilot Pro**: Autonomous multi-language code assistant.\n- **NeuralVoice Studio 4D**: Human-parity voice cloning.\n";
            }

            $reply = "### 🚀 Featured AI Tools on Dyzzstore\n\n"
                ."We host 45+ cutting-edge verified AI models and software:\n\n"
                .$toolsText."\n"
                .'You can buy lifetime access to individual tools or get everything unlocked with the Pro AI Suite!';

            return [
                'reply' => $reply,
                'quick_replies' => ['How do I pay with KHQR?', 'Single tool vs Pro Suite', 'Contact human support'],
                'actions' => [
                    ['type' => 'browse_tools', 'label' => '🛍️ Explore All AI Tools'],
                ],
            ];
        }

        // 7. Refund Policy & Commercial Rights
        if (str_contains($lower, 'refund') || str_contains($lower, 'money back') || str_contains($lower, 'guarantee')) {
            $reply = "### 🛡️ 14-Day Money-Back Guarantee\n\n"
                ."Yes! We offer an unconditional **14-day 100% money-back guarantee** on all perpetual tool purchases.\n\n"
                ."If any AI tool does not meet your workflow expectations, simply reach out to our support team or click 'Request Refund' inside your account dashboard for a prompt, hassle-free refund.";

            return [
                'reply' => $reply,
                'quick_replies' => ['How do I pay with KHQR?', 'Contact human support', 'Commercial rights?'],
            ];
        }

        if (str_contains($lower, 'commercial') || str_contains($lower, 'copyright') || str_contains($lower, 'rights') || str_contains($lower, 'ownership')) {
            $reply = "### ⚖️ 100% Commercial Rights Ownership\n\n"
                ."All text, code, audio, 3D assets, and photorealistic imagery created with Dyzzstore AI tools are **100% owned by you**.\n\n"
                .'You have unrestricted rights to sell, license, trademark, monetize, and distribute generated outputs without paying royalties or attributing Dyzzstore.';

            return [
                'reply' => $reply,
                'quick_replies' => ['How do I pay with KHQR?', 'Browse AI Tools', 'Contact human support'],
            ];
        }

        // 8. Human Support / Telegram
        if (
            str_contains($lower, 'contact') ||
            str_contains($lower, 'support') ||
            str_contains($lower, 'human') ||
            str_contains($lower, 'admin') ||
            str_contains($lower, 'telegram') ||
            str_contains($lower, 'chett') ||
            str_contains($lower, 'help') ||
            str_contains($lower, 'talk to someone')
        ) {
            $reply = "### 💬 Contact Human Support\n\n"
                ."Our dedicated support team is available 24/7 on Telegram!\n\n"
                ."- **Direct Telegram Support**: [@{$telegramUsername}](https://t.me/{$telegramUsername})\n"
                ."- **Email Support**: support@dyzzstore.store\n"
                ."- **Response Time**: Usually under 5 minutes\n\n"
                .'Feel free to message our administrator directly for custom enterprise licensing, invoice requests, or payment assistance.';

            return [
                'reply' => $reply,
                'quick_replies' => ['How do I pay with KHQR?', 'What payment methods?', 'Browse AI Tools'],
                'actions' => [
                    ['type' => 'link', 'url' => "https://t.me/{$telegramUsername}", 'label' => '✈️ Open Telegram Support'],
                ],
            ];
        }

        // Default Friendly Greeting / Fallback
        $reply = "Hello! 👋 I'm your **Dyzzstore AI Support Assistant**.\n\n"
            ."I'm here to assist you with anything you need:\n"
            ."- 💳 **How to pay** via Cambodia National KHQR (ABA, Bakong, ACLEDA, etc.)\n"
            ."- ⚡ **Instant license key & API delivery** process\n"
            ."- 💎 **Single tool licenses vs Pro Subscription**\n"
            ."- 🛡️ **Refund policy & 100% commercial rights**\n"
            ."- 💬 **Direct human support on Telegram (@{$telegramUsername})**\n\n"
            .'How can I help you today? Feel free to type any question or choose a topic below!';

        return [
            'reply' => $reply,
            'quick_replies' => [
                '💳 How do I pay with KHQR?',
                '⚡ How do I get my license key?',
                '💎 Pro Suite vs Single Tool',
                '🛡️ Money-back guarantee',
                '💬 Contact human support',
            ],
            'actions' => [
                ['type' => 'open_checkout', 'label' => '💳 Start Checkout'],
                ['type' => 'browse_tools', 'label' => '🛍️ Browse AI Tools'],
            ],
        ];
    }
}
