<?php
/**
 * Admin area view for the Active Plugin
 */

// Prevent direct access
if (!defined('WPINC')) {
    die;
}
?>

<div class="wrap">
    <h1>Quản lý mã kích hoạt</h1>
    
    <div class="card">
        <h2>Tạo mã kích hoạt mới</h2>
        <form id="create-key-active-form">
            <table class="form-table">
                <tr>
                    <th><label for="title">Mã kích hoạt:</label></th>
                    <td><input type="text" id="title" name="title" required></td>
                </tr>
                <tr>
                    <th><label for="email">Email:</label></th>
                    <td><input type="email" id="email" name="email"></td>
                </tr>
                <tr>
                    <th><label for="person">Người dùng:</label></th>
                    <td><input type="text" id="person" name="person"></td>
                </tr>
                <tr>
                    <th><label for="plugin_id">Plugin ID:</label></th>
                    <td><input type="text" id="plugin_id" name="plugin_id" required></td>
                </tr>
                <tr>
                    <th><label for="plugin_name">Tên Plugin:</label></th>
                    <td><input type="text" id="plugin_name" name="plugin_name" required></td>
                </tr>
                <tr>
                    <th><label for="domain">Tên miền:</label></th>
                    <td><input type="text" id="domain" name="domain" required></td>
                </tr>
                <tr>
                    <th><label for="version">Phiên bản:</label></th>
                    <td><input type="text" id="version" name="version"></td>
                </tr>
                <tr>
                    <th><label for="status">Trạng thái:</label></th>
                    <td><input type="checkbox" id="status" name="status" checked></td>
                </tr>
                <tr>
                    <th><label for="expire">Ngày hết hạn:</label></th>
                    <td><input type="date" id="expire" name="expire"></td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" class="button button-primary" value="Tạo mã kích hoạt">
                <span class="spinner"></span>
            </p>
        </form>
    </div>
    
    <div class="card">
        <h2>Danh sách mã kích hoạt</h2>
        <?php
        $args = array(
            'post_type' => 'key_active',
            'posts_per_page' => -1,
        );
        $query = new WP_Query($args);
        
        if ($query->have_posts()) :
        ?>
        <table class="widefat fixed" cellspacing="0">
            <thead>
                <tr>
                    <th>Mã kích hoạt</th>
                    <th>Tên Plugin</th>
                    <th>Tên miền</th>
                    <th>Ngày hết hạn</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($query->have_posts()) : $query->the_post(); 
                    $post_id = get_the_ID();
                    $plugin_name = get_post_meta($post_id, 'plugin_name', true);
                    $domain = get_post_meta($post_id, 'domain', true);
                    $expire = get_post_meta($post_id, 'expire', true);
                    $status = get_post_meta($post_id, 'status', true);
                ?>
                <tr>
                    <td><?php the_title(); ?></td>
                    <td><?php echo esc_html($plugin_name); ?></td>
                    <td><?php echo esc_html($domain); ?></td>
                    <td><?php echo esc_html($expire); ?></td>
                    <td><?php echo ($status == 'true') ? '<span style="color: green;">Hoạt động</span>' : '<span style="color: red;">Vô hiệu</span>'; ?></td>
                    <td>
                        <a href="<?php echo get_edit_post_link($post_id); ?>" class="button">Sửa</a>
                        <button class="button delete-post" data-post-id="<?php echo $post_id; ?>">Xóa</button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p>Chưa có mã kích hoạt nào.</p>
        <?php endif; wp_reset_postdata(); ?>
    </div>
</div>