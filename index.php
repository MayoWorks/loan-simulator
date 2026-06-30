<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

function loan_tool() {


    /* ==========================
入力値取得
========================== */

    $loanMan = isset($_POST['loan']) ? floatval($_POST['loan']) : 300;
    $loan = $loanMan * 10000;

    $rate = isset($_POST['rate']) ? floatval($_POST['rate']) / 100 : 0.049;

    $months = isset($_POST['months']) ? intval($_POST['months']) : 60;

    $payoffMonth = isset($_POST['payoffMonth']) ? intval($_POST['payoffMonth']) : 36;

    /* 押されたローン会社 */

    $loanType = isset($_POST['loanType']) ? $_POST['loanType'] : "";


    /* ==========================
基本ローン計算
========================== */

    $monthlyRate = $rate / 12;

    $payment = ($monthlyRate * $loan) / (1 - pow(1 + $monthlyRate, -$months));

    $balance = $loan;

    $paidTotal = 0;

    $payoffBalance = 0;


    /* 残債計算 */

    for ($i = 1; $i <= $months; $i++) {

        $interest = $balance * $monthlyRate;

        $principal = $payment - $interest;

        $balance -= $principal;

        if ($i <= $payoffMonth) {
            $paidTotal += $payment;
        }

        if ($i == $payoffMonth) {
            $payoffBalance = $balance;
        }
    }

    if ($payoffBalance < 0) {
        $payoffBalance = 0;
    }


    /* ==========================
信販会社別計算
========================== */

    $resultBalance = $payoffBalance;

    $explain = "";


    /* Company A */

    if ($loanType == "ca") {

        $dailyInterest = $payoffBalance * 0.0001;

        $resultBalance = $payoffBalance + $dailyInterest;

        $explain = "概算です。公開されている情報をもとに計算しています。実際の一括返済額は契約先へお問い合わせください。";
    }


    /* Company B */

    if ($loanType == "cb") {

        $resultBalance = $payoffBalance + 10000;

        $explain = "概算です。公開されている情報をもとに計算しています。実際の一括返済額は契約先へお問い合わせください。";
    }


    /* Company C */

    if ($loanType == "cc") {

        $fee = 5000;

        $rule78 = $payoffBalance * 0.02;

        $resultBalance = $payoffBalance + $fee + $rule78;

        $explain = "概算です。公開されている情報をもとに計算しています。実際の一括返済額は契約先へお問い合わせください。";
    }


    /* ==========================
★ここから表示調整（重要）
========================== */

    // 【調整①】安全のため1%上乗せ（クレーム防止）
    $resultBalance = $resultBalance * 1.01;

    // 【調整②】万円単位に変換＆千円以下切り上げ
    $resultBalance_man = ceil($resultBalance / 10000);

    // 【調整②】支払い総額も同様に処理
    $paidTotal_safe = $paidTotal * 1.01;
    $paidTotal_man = ceil($paidTotal_safe / 10000);


    ob_start();
?>
    <!DOCTYPE html>
    <html lang="ja">


    <head>
        <meta charset="UTF-8">
        <title>ローンシミュレーター</title>

        <link rel="stylesheet" href="style.css">

    </head>

    <body>
        <div class="head">

            <h3>概算で残債と一括返済額を計算します</h3>
            <h3>(ʘ‿ʘ) ボクがけいさんするよ</h3>
            <h3>(ʘ‿ʘ)ネットでこうかいされてるすうしきでけいさんしてるよ</h3>
        </div>

        <form method="post" class="loan-form">

            <div class="loan-group">

                <label class="loan-label">
                    いくら借りていますか？（万円）
                </label>

                <input
                    class="loan-input"
                    name="loan"
                    value="<?php echo $loanMan; ?>">

            </div>

            <div class="loan-group">

                <label class="loan-label">
                    金利は？（%）
                </label>

                <input
                    class="loan-input"
                    name="rate"
                    value="<?php echo $rate * 100; ?>">

            </div>
            <div class="loan-group">

                <label class="loan-label">
                    何回ローンですか？
                </label>

                <input
                    class="loan-input"
                    name="month"
                    value="<?php echo $months; ?>">
            </div>

            <div class="loan-group">

                <label class="loan-label">
                    現在何回目ですか？
                </label>

                <input
                    class="loan-input"
                    name="payoffMonth"
                    value="<?php echo $payoffMonth; ?>">
            </div>


            <div>
                <button
                    type="submit"
                    name="loanType"
                    value="ca"
                    class="loan-btn ca">
                    Conmany A で計算
                </button>
                <button
                    type="submit"
                    name="loanType"
                    value="cb"
                    class="loan-btn cb">
                    Company B で計算
                </button>
                <button
                    type="submit"
                    name="loanType"
                    value="cc"
                    class="loan-btn cc">
                    Company C で計算
                </button>
            </div>

            <h3 class="result-title">
                いま一括返済すると
            </h3>

            <div class="result-box">

                <div class="result-price">
                    <?php
                    // 【調整③】「約」を付けた表示
                    echo "約 " . number_format($resultBalance_man) . " 万円";
                    ?>
                </div>

            </div>

            <div class="result-total">

                ここまでの支払総額<br>

                <span>
                    <?php
                    echo "約 " . number_format($paidTotal_man) . " 万円";
                    ?>
                </span>

            </div>

            <div class="result-comment">
                (ʘ‿ʘ) ボクにわかるのはここまでだよ
            </div>
        </form>
        <div class="result-note">
            <?php echo $explain; ?>
        </div>
        </div>
    </body>

    </html>

<?php

    return ob_get_clean();
}   

echo loan_tool();

?>
