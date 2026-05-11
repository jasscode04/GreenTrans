    </div> <!-- end gt-content -->
</main> <!-- end gt-main -->
</div> <!-- end gt-layout -->

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<!-- GreenTrans JS -->
<script src="<?= APP_URL ?>/assets/js/main.js"></script>

<?php if (defined('LOAD_CHART_JS') && LOAD_CHART_JS): ?>
<script src="<?= APP_URL ?>/assets/js/dashboard.js"></script>
<?php endif; ?>

</body>
</html>
