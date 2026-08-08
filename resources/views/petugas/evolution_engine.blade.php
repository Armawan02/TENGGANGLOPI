@extends('layouts.petugas')

@section('title', 'Evolution Engine')
@section('subtitle', 'AI Predictive Analytics & Machine Learning Models')

@section('styles')
    <style>
        .engine-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 25px;
        }
        @media (max-width: 1024px) {
            .engine-grid { grid-template-columns: 1fr; }
        }
        
        .engine-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 24px;
        }
        
        .card-title {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        /* Model Status */
        .model-list { display: flex; flex-direction: column; gap: 15px; }
        .model-item {
            background: var(--bg-main);
            border: 1px solid var(--border-color);
            padding: 15px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .model-info h4 { font-size: 14px; color: var(--text-primary); margin-bottom: 4px; }
        .model-info p { font-size: 12px; color: var(--text-muted); }
        .model-status {
            text-align: right;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .status-badge.training { background: rgba(59, 130, 246, 0.15); color: var(--accent); border: 1px solid rgba(59, 130, 246, 0.3); }
        .status-badge.active { background: rgba(52, 211, 153, 0.15); color: var(--success); border: 1px solid rgba(52, 211, 153, 0.3); }
        
        /* Fake Chart Area */
        .chart-container {
            height: 300px;
            width: 100%;
            background: var(--bg-main);
            border: 1px dashed var(--border-color);
            border-radius: 8px;
            display: flex;
            align-items: flex-end;
            padding: 20px;
            gap: 10px;
            position: relative;
        }
        
        .bar {
            flex: 1;
            background: linear-gradient(to top, rgba(59, 130, 246, 0.2), var(--accent));
            border-radius: 4px 4px 0 0;
            position: relative;
            transition: 0.5s;
        }
        .bar:hover { opacity: 0.8; }
        .bar span {
            position: absolute;
            top: -20px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 10px;
            color: var(--text-muted);
        }
        
        /* Prediction List */
        .prediction-item {
            padding: 12px 0;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .prediction-item:last-child { border-bottom: none; }
        .pred-target { font-size: 13px; font-weight: 600; color: var(--text-primary); }
        .pred-prob { font-size: 14px; font-weight: 700; color: var(--accent); }
    </style>
@endsection

@section('content')
<div class="engine-grid">
    <div style="display: flex; flex-direction: column; gap: 25px;">
        <div class="engine-card">
            <div class="card-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.21 15.89A10 10 0 1 1 8 2.83"></path><path d="M22 12A10 10 0 0 0 12 2v10z"></path></svg>
                AI Prediction Accuracy (Last 7 Days)
            </div>
            
            <div class="chart-container">
                <!-- Decorative grid lines -->
                <div style="position:absolute; width:100%; border-top: 1px solid rgba(255,255,255,0.05); top: 25%;"></div>
                <div style="position:absolute; width:100%; border-top: 1px solid rgba(255,255,255,0.05); top: 50%;"></div>
                <div style="position:absolute; width:100%; border-top: 1px solid rgba(255,255,255,0.05); top: 75%;"></div>
                
                <div class="bar" style="height: 65%;"><span>65%</span></div>
                <div class="bar" style="height: 72%;"><span>72%</span></div>
                <div class="bar" style="height: 85%;"><span>85%</span></div>
                <div class="bar" style="height: 81%;"><span>81%</span></div>
                <div class="bar" style="height: 92%;"><span>92%</span></div>
                <div class="bar" style="height: 88%;"><span>88%</span></div>
                <div class="bar" style="height: 95%;"><span>95%</span></div>
            </div>
        </div>

        <div class="engine-card">
            <div class="card-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                Machine Learning Models
            </div>
            <div class="model-list">
                <div class="model-item">
                    <div class="model-info">
                        <h4>Wave Pattern Recognition v2.4</h4>
                        <p>LSTM Neural Network untuk memprediksi gelombang tinggi berdasarkan data angin.</p>
                    </div>
                    <div class="model-status">
                        <span class="status-badge active">Deployed</span>
                        <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">Loss: 0.042</div>
                    </div>
                </div>
                <div class="model-item">
                    <div class="model-info">
                        <h4>Hardware Failure Predictor</h4>
                        <p>Random Forest model untuk mendeteksi degradasi baterai & sensor LoRa.</p>
                    </div>
                    <div class="model-status">
                        <span class="status-badge training">Training (EPOCH 14/50)</span>
                        <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">ETA: 2h 15m</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="engine-card">
        <div class="card-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
            Live Risk Predictions
        </div>
        
        <p style="font-size: 12px; color: var(--text-muted); margin-bottom: 20px;">
            Probabilitas anomali dalam 24 jam ke depan berdasarkan data historis dan cuaca real-time.
        </p>

        <div>
            <div class="prediction-item">
                <div>
                    <div class="pred-target">Badai di Sektor Majene Selatan</div>
                    <div style="font-size: 11px; color: var(--text-muted);">Confidence Score</div>
                </div>
                <div class="pred-prob" style="color: var(--danger);">87.4%</div>
            </div>
            <div class="prediction-item">
                <div>
                    <div class="pred-target">Kegagalan Baterai Node_01</div>
                    <div style="font-size: 11px; color: var(--text-muted);">Confidence Score</div>
                </div>
                <div class="pred-prob" style="color: var(--warning);">62.1%</div>
            </div>
            <div class="prediction-item">
                <div>
                    <div class="pred-target">Sinyal LoRa Drop Area Polman</div>
                    <div style="font-size: 11px; color: var(--text-muted);">Confidence Score</div>
                </div>
                <div class="pred-prob">45.0%</div>
            </div>
            <div class="prediction-item">
                <div>
                    <div class="pred-target">Cuaca Cerah Sektor Utara</div>
                    <div style="font-size: 11px; color: var(--text-muted);">Confidence Score</div>
                </div>
                <div class="pred-prob" style="color: var(--success);">94.8%</div>
            </div>
        </div>
        
        <button style="width: 100%; margin-top: 25px; padding: 12px; background: rgba(255,255,255,0.05); border: 1px solid var(--border-color); color: var(--text-primary); border-radius: 8px; cursor: pointer; transition: 0.2s;">
            Force Re-Evaluate All Nodes
        </button>
    </div>
</div>
@endsection
