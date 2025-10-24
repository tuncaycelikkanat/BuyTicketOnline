<footer>
    <p>&copy; <?php echo date('Y'); ?> Tuncay Çelikkanat. All Rights Reserved.</p>
</footer>
</body>

</html>
<style>
    html,
    body {
        height: 100%;
        margin: 0;
    }

    body {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        background-color: #0a0a0a;
        color: #fff;
    }

    main {
        flex: 1;
    }

    footer {
        background-color: #1a1a1a;
        padding: 20px 0;
        text-align: center;
        border-top: 2px solid #0ff;
        color: #f0f0f0;
    }

    footer p {
        margin: 0 0 10px 0;
        font-size: 14px;
        color: #aaa;
    }
</style>