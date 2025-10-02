<?php

/**
 * Presentation Generator Service
 *
 * Service for auto-generating PowerPoint presentations from report data.
 * Converts report data into structured presentation content.
 *
 * @category Services
 * @package  Sellora\Services
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */

namespace App\Services;

use App\Models\Presentation;
use App\Models\Report;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * PresentationGeneratorService
 *
 * Handles auto-generation of presentations from report data
 *
 * @category Services
 * @package  Sellora\Services
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */
class PresentationGeneratorService
{
    /**
     * Generate presentation from report data
     *
     * @param Report  $report  The report to generate presentation from
     * @param array   $options Generation options
     *
     * @return Presentation
     */
    public function generateFromReport(Report $report, array $options = []): Presentation
    {
        // Get report data
        $reportData = $this->_getReportData($report);
        
        // Generate presentation content
        $content = $this->_generatePresentationContent($reportData, $options);
        
        // Create presentation file
        $filePath = $this->_createPresentationFile($content, $report->title ?? 'Generated Report');
        
        // Create presentation record
        $presentation = Presentation::create(
             [
                 'title' => ($options['title'] ?? $report->title ?? 'Generated Report') . ' - Presentation',
                 'description' => 'Auto-generated presentation from ' . ($report->title ?? 'report'),
                 'content' => $content,
                 'file_path' => $filePath,
                 'original_filename' => Str::slug($report->title ?? 'report') . '_presentation.html',
                 'file_size' => strlen($content),
                 'mime_type' => 'text/html',
                 'category' => 'Report',
                 'tags' => ['auto-generated', 'report', $report->type ?? 'general'],
                 'status' => 'published',
                 'privacy_level' => $options['privacy_level'] ?? 'private',
                 'is_public' => $options['is_public'] ?? false,
                 'is_template' => false,
                 'user_id' => Auth::id(),
                 'generated_from_report_id' => $report->id
             ]
         );
        
        return $presentation;
    }
    
    /**
     * Get report data for presentation generation
     *
     * @param Report $report The report model
     *
     * @return array
     */
    private function _getReportData(Report $report): array
    {
        // Parse report filters and get data
        $filters = json_decode($report->filters, true) ?? [];
        
        // This would typically fetch the actual report data
        // For now, we'll create sample data structure
        return [
            'title' => $report->title ?? 'Report',
            'type' => $report->type ?? 'general',
            'filters' => $filters,
            'generated_at' => $report->created_at,
            'user' => $report->user,
            'data' => $this->_getMockReportData($report->type ?? 'general')
        ];
    }
    
    /**
     * Generate presentation content from report data
     *
     * @param array $reportData The report data
     * @param array $options    Generation options
     *
     * @return string
     */
    private function _generatePresentationContent(array $reportData, array $options = []): string
    {
        $template = $options['template'] ?? 'default';
        
        $content = $this->_getBaseTemplate();
        
        // Replace placeholders with actual data
        $content = str_replace('{{TITLE}}', $reportData['title'], $content);
        $content = str_replace('{{GENERATED_DATE}}', now()->format('F j, Y'), $content);
        $content = str_replace('{{USER_NAME}}', $reportData['user']->name ?? 'Unknown', $content);
        
        // Generate slides from data
        $slides = $this->_generateSlides($reportData['data']);
        $content = str_replace('{{SLIDES}}', $slides, $content);
        
        return $content;
    }
    
    /**
     * Generate slides from report data
     *
     * @param array $data The report data
     *
     * @return string
     */
    private function _generateSlides(array $data): string
    {
        $slides = '';
        
        // Summary slide
        $slides .= $this->_generateSummarySlide($data);
        
        // Data slides
        foreach ($data as $section => $sectionData) {
            if (is_array($sectionData) && !empty($sectionData)) {
                $slides .= $this->_generateDataSlide($section, $sectionData);
            }
        }
        
        // Conclusion slide
        $slides .= $this->_generateConclusionSlide($data);
        
        return $slides;
    }
    
    /**
     * Generate summary slide
     *
     * @param  array $data The report data
     *
     * @return string
     */
    private function _generateSummarySlide(array $data): string
    {
        return '
        <div class="slide">
            <h2>Executive Summary</h2>
            <div class="slide-content">
                <ul>
                    <li>Total Records: ' . count($data) . '</li>
                    <li>Report Generated: ' . now()->format('F j, Y') . '</li>
                    <li>Data Period: Last 30 days</li>
                </ul>
            </div>
        </div>';
    }
    
    /**
     * Generate data slide for a section
     *
     * @param string $section     The section name
     * @param array  $sectionData The section data
     *
     * @return string
     */
    private function _generateDataSlide(string $section, array $sectionData): string
    {
        $content = '
        <div class="slide">
            <h2>' . ucfirst($section) . ' Analysis</h2>
            <div class="slide-content">';
        
        if (is_numeric(array_keys($sectionData)[0] ?? null)) {
            // Numeric array - create table
            $content .= '<table class="data-table">
                <thead><tr><th>Item</th><th>Value</th></tr></thead>
                <tbody>';
            
            foreach (array_slice($sectionData, 0, 10) as $index => $item) {
                $content .= '<tr><td>Item ' . ($index + 1) . '</td><td>' . (is_array($item) ? json_encode($item) : $item) . '</td></tr>';
            }
            
            $content .= '</tbody></table>';
        } else {
            // Associative array - create key-value list
            $content .= '<ul class="key-value-list">';
            
            foreach (array_slice($sectionData, 0, 8, true) as $key => $value) {
                $content .= '<li><strong>' . ucfirst($key) . ':</strong> ' . (is_array($value) ? count($value) . ' items' : $value) . '</li>';
            }
            
            $content .= '</ul>';
        }
        
        $content .= '</div></div>';
        
        return $content;
    }
    
    /**
     * Generate conclusion slide
     *
     * @param  array $data The report data
     *
     * @return string
     */
    private function _generateConclusionSlide(array $data): string
    {
        return '
        <div class="slide">
            <h2>Conclusion</h2>
            <div class="slide-content">
                <ul>
                    <li>Report analysis completed successfully</li>
                    <li>Data insights extracted from ' . count($data) . ' data points</li>
                    <li>Presentation auto-generated by Sellora system</li>
                </ul>
                <p class="note">This presentation was automatically generated from report data. For detailed analysis, please refer to the original report.</p>
            </div>
        </div>';
    }
    
    /**
     * Create presentation file
     *
     * @param string $content  The presentation content
     * @param string $filename The filename
     *
     * @return string
     */
    private function _createPresentationFile(string $content, string $filename): string
    {
        $filename = Str::slug($filename) . '_' . time() . '.html';
        $filePath = 'presentations/generated/' . $filename;
        
        Storage::disk('public')->put($filePath, $content);
        
        return $filePath;
    }
    
    /**
     * Get base HTML template for presentations
     *
     * @return string
     */
    private function _getBaseTemplate(): string
    {
        return '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{TITLE}}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }
        .presentation { max-width: 1200px; margin: 0 auto; }
        .slide { background: white; margin: 20px 0; padding: 40px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); page-break-after: always; }
        .slide h1 { color: #2c3e50; border-bottom: 3px solid #3498db; padding-bottom: 10px; }
        .slide h2 { color: #34495e; margin-top: 0; }
        .slide-content { margin-top: 20px; }
        .data-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .data-table th, .data-table td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        .data-table th { background: #3498db; color: white; }
        .key-value-list { list-style: none; padding: 0; }
        .key-value-list li { padding: 8px 0; border-bottom: 1px solid #eee; }
        .note { font-style: italic; color: #7f8c8d; margin-top: 20px; }
        @media print { .slide { page-break-after: always; } }
    </style>
</head>
<body>
    <div class="presentation">
        <div class="slide">
            <h1>{{TITLE}}</h1>
            <div class="slide-content">
                <p><strong>Generated:</strong> {{GENERATED_DATE}}</p>
                <p><strong>Created by:</strong> {{USER_NAME}}</p>
                <p><strong>System:</strong> Sellora Auto-Generated Presentation</p>
            </div>
        </div>
        {{SLIDES}}
    </div>
</body>
</html>';
    }
    
    /**
     * Get mock report data for testing
     *
     * @param string $type The report type
     *
     * @return array
     */
    private function _getMockReportData(string $type): array
    {
        return [
            'sales' => [
                'total_sales' => 150000,
                'total_orders' => 45,
                'average_order_value' => 3333,
                'top_products' => ['Product A', 'Product B', 'Product C']
            ],
            'performance' => [
                'conversion_rate' => '12.5%',
                'customer_satisfaction' => '4.2/5',
                'return_rate' => '2.1%'
            ],
            'trends' => [
                'monthly_growth' => '+15%',
                'quarterly_performance' => 'Above target',
                'market_share' => '8.3%'
            ]
        ];
    }
}