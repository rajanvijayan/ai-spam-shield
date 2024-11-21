<?php
namespace AISpamShield\Admin;

class SettingsPage {
    public function __construct() {
        add_action( 'admin_menu', [ $this, 'add_settings_page' ] );
        add_action( 'admin_init', [ $this, 'register_settings' ] );
    }

    public function add_settings_page() {
        add_options_page(
            'Comment Moderator Settings',
            'Comment Moderator',
            'manage_options',
            'ai-comment-moderator',
            [ $this, 'create_settings_page' ]
        );
    }

    public function register_settings() {
        register_setting( 'ai_comment_moderator_group', 'auto_response' );
        register_setting( 'ai_comment_moderator_group', 'response_mode' );
        register_setting( 'ai_comment_moderator_group', 'cron_schedule_time' );
        register_setting( 'ai_comment_moderator_group', 'api_key' );
        register_setting( 'ai_comment_moderator_group', 'moderator_user' );
        register_setting( 'ai_comment_moderator_group', 'enable_email_spam_protection' );
        register_setting( 'ai_comment_moderator_group', 'disable_for_logged_in_users' );
        register_setting( 'ai_comment_moderator_group', 'disable_for_wp_emails' );
    }

    public function create_settings_page() {
        ?>
        <div class="wrap">
            <h1>Comment Moderator Settings</h1>

            <!-- Tabs for Basic Settings and API Settings -->
            <h2 class="nav-tab-wrapper">
                <a href="#basic-settings" class="nav-tab nav-tab-active">Basic Settings</a>
                <a href="#email-settings" class="nav-tab">Email Settings</a>
                <a href="#api-settings" class="nav-tab">API Settings</a>
            </h2>

            <form method="post" action="options.php">
                <?php settings_fields( 'ai_comment_moderator_group' ); ?>
                <?php do_settings_sections( 'ai_comment_moderator_group' ); ?>

                <!-- Basic Settings Tab -->
                <div id="basic-settings" class="settings-section" style="display:block;">
                    <h2>Basic Settings</h2>
                    <p>Configure the comment moderation settings, including auto-response options and cron schedule times.</p>
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row">Auto Response</th>
                            <td>
                                <input type="checkbox" name="auto_response" value="1" <?php checked( get_option( 'auto_response' ), 1 ); ?> />
                                <p class="description">Enable this to automatically respond to new comments.</p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Response Mode</th>
                            <td>
                            <select name="response_mode">
                                <option value="professional" <?php selected( get_option( 'response_mode' ), 'professional' ); ?>>Professional - Formal and respectful</option>
                                <option value="friendly" <?php selected( get_option( 'response_mode' ), 'friendly' ); ?>>Friendly - Warm and approachable</option>
                                <option value="humorous" <?php selected( get_option( 'response_mode' ), 'humorous' ); ?>>Humorous - Lighthearted and funny</option>
                                <option value="sarcastic" <?php selected( get_option( 'response_mode' ), 'sarcastic' ); ?>>Sarcastic - Witty and playful</option>
                                <option value="inspirational" <?php selected( get_option( 'response_mode' ), 'inspirational' ); ?>>Inspirational - Uplifting and motivational</option>
                                <option value="concise" <?php selected( get_option( 'response_mode' ), 'concise' ); ?>>Concise - Brief and to the point</option>
                                <option value="empathetic" <?php selected( get_option( 'response_mode' ), 'empathetic' ); ?>>Empathetic - Understanding and compassionate</option>
                                <option value="curious" <?php selected( get_option( 'response_mode' ), 'curious' ); ?>>Curious - Inquisitive and questioning</option>
                                <option value="supportive" <?php selected( get_option( 'response_mode' ), 'supportive' ); ?>>Supportive - Encouraging and reassuring</option>
                                <option value="informative" <?php selected( get_option( 'response_mode' ), 'informative' ); ?>>Informative - Detailed and educational</option>
                                <option value="formal" <?php selected( get_option( 'response_mode' ), 'formal' ); ?>>Formal - Highly professional and structured</option>
                                <option value="casual" <?php selected( get_option( 'response_mode' ), 'casual' ); ?>>Casual - Relaxed and informal</option>
                                <option value="neutral" <?php selected( get_option( 'response_mode' ), 'neutral' ); ?>>Neutral - Balanced and unbiased</option>
                                <option value="optimistic" <?php selected( get_option( 'response_mode' ), 'optimistic' ); ?>>Optimistic - Positive and hopeful</option>
                                <option value="pessimistic" <?php selected( get_option( 'response_mode' ), 'pessimistic' ); ?>>Pessimistic - Realistic and cautious</option>
                                <option value="assertive" <?php selected( get_option( 'response_mode' ), 'assertive' ); ?>>Assertive - Confident and direct</option>
                                <option value="respectful" <?php selected( get_option( 'response_mode' ), 'respectful' ); ?>>Respectful - Polite and considerate</option>
                                <option value="grateful" <?php selected( get_option( 'response_mode' ), 'grateful' ); ?>>Grateful - Thankful and appreciative</option>
                                <option value="conversational" <?php selected( get_option( 'response_mode' ), 'conversational' ); ?>>Conversational - Chatty and informal</option>
                                <option value="sympathetic" <?php selected( get_option( 'response_mode' ), 'sympathetic' ); ?>>Sympathetic - Expresses concern and understanding</option>
                            </select>
                                <p class="description">Choose the tone of the automatic responses.</p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Cron Schedule Time</th>
                            <td>
                                <select name="cron_schedule_time">
                                    <?php
                                    $schedules = wp_get_schedules();
                                    $selected_schedule = get_option( 'cron_schedule_time', 'hourly' );
                                    foreach ( $schedules as $schedule_key => $schedule_data ) {
                                        $selected = selected( $selected_schedule, $schedule_key, false );
                                        echo "<option value='{$schedule_key}' $selected>{$schedule_data['display']}</option>";
                                    }
                                    ?>
                                </select>
                                <p class="description">How often should the system check for new comments? Choose a schedule.</p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Select Moderator</th>
                            <td>
                                <select name="moderator_user">
                                    <option value="">Select a Moderator</option>
                                    <?php
                                    $users = get_users( [
                                        'role__in' => ['administrator', 'editor', 'moderator'],
                                        'orderby' => 'display_name',
                                        'order' => 'ASC',
                                    ] );
                                    foreach ( $users as $user ) {
                                        $selected = selected( get_option( 'moderator_user' ), $user->ID, false );
                                        echo "<option value='{$user->ID}' $selected>{$user->display_name}</option>";
                                    }
                                    ?>
                                </select>
                                <p class="description">Select the user who will moderate comments. Make sure a valid user is selected.</p>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Email Settings Tab -->
                <div id="email-settings" class="settings-section" style="display:none;">
                    <h2>Email Settings</h2>
                    <p>Configure email related settings for better spam protection.</p>
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row">Enable Email Spam Protection</th>
                            <td>
                                <input type="checkbox" name="enable_email_spam_protection" value="1" <?php checked(get_option('enable_email_spam_protection'), 1); ?> />
                                <p class="description">Enable this to automatically filter spam in emails.</p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Disable Checking for Logged In Users</th>
                            <td>
                                <input type="checkbox" name="disable_for_logged_in_users" value="1" <?php checked(get_option('disable_for_logged_in_users'), 1); ?> />
                                <p class="description">Disable spam checking for users that are logged in.</p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Disable Checking for WordPress Emails</th>
                            <td>
                                <input type="checkbox" name="disable_for_wp_emails" value="1" <?php checked(get_option('disable_for_wp_emails'), 1); ?> />
                                <p class="description">Exclude WordPress system emails like password reset and user registration from spam checking.</p>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- API Settings Tab -->
                <div id="api-settings" class="settings-section" style="display:none;">
                    <h2>API Settings</h2>
                    <p class="description">Enter your API key. Ensure it's correct to avoid errors in moderation.</p>
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row">API Key</th>
                            <td>
                                <input type="password" name="api_key" class="regular-text" value="<?php echo esc_attr( get_option( 'api_key' ) ); ?>" />
                                <p class="description">Enter your API Secret Key here. <a href="https://aistudio.google.com/app/apikey" target="_blank">Get your API key here</a></p>
                            </td>
                        </tr>
                    </table>
                </div>

                <?php submit_button(); ?>
            </form>
        </div>

        <script>
            // JavaScript to handle the tab navigation
            document.querySelectorAll('.nav-tab').forEach(tab => {
                tab.addEventListener('click', function (e) {
                    e.preventDefault();

                    document.querySelectorAll('.nav-tab').forEach(t => t.classList.remove('nav-tab-active'));
                    tab.classList.add('nav-tab-active');

                    document.querySelectorAll('.settings-section').forEach(section => section.style.display = 'none');
                    document.querySelector(tab.getAttribute('href')).style.display = 'block';
                });
            });
        </script>
        <?php
    }
}